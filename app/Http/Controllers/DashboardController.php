<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Models\Room;
use App\Models\College;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Equipment;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        try {
            return [
                'totalAccounts' => UserAccount::count(),
                'totalDepartments' => Department::count(),
                'totalColleges' => College::count(),
                'totalRooms' => Room::count(),
                'totalBuildings' => Building::count(),
                'totalEquipment' => Equipment::count(),
                'totalSchedules' => Schedule::count(),
                'activeSchedules' => Schedule::where('status', 'approved')->count(),
                'pendingSchedules' => Schedule::where('status', 'pending')->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return [
                'totalAccounts' => 0,
                'totalDepartments' => 0,
                'totalColleges' => 0,
                'totalRooms' => 0,
                'totalBuildings' => 0,
                'totalEquipment' => 0,
                'totalSchedules' => 0,
                'activeSchedules' => 0,
                'pendingSchedules' => 0,
            ];
        }
    }

    public function getRooms(Request $request)
    {
        $search = $request->query('search');
        $query = Room::with(['building:id,building_name', 'college:id,college_name'])
            ->orderBy('room_name');

        if ($search) {
            $query->where('room_name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%");
        }

        return response()->json($query->paginate(5));
    }

    public function search(Request $request)
    {
        $search = $request->query('q');

        if (!$search) {
            return response()->json([]);
        }

        $results = [];

        // Search in Rooms
        $rooms = Room::where('room_name', 'like', "%{$search}%")
            ->orWhere('room_code', 'like', "%{$search}%")
            ->limit(5)
            ->get()
            ->map(function ($room) {
                return [
                    'type' => 'room',
                    'id' => $room->id,
                    'name' => $room->room_name,
                    'code' => $room->room_code,
                    'url' => '/rooms/' . $room->id
                ];
            });

        // Search in Users
        $users = UserAccount::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere('username', 'like', "%{$search}%")
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'url' => '/users/' . $user->id
                ];
            });

        $results = $rooms->merge($users);

        return response()->json($results);
    }
}

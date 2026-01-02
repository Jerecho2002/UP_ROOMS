<?php

namespace App\Services;

use App\Models\UserAccount;
use App\Models\Room;
use App\Models\College;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Equipment;
use App\Models\Building;
use Illuminate\Support\Facades\DB;

class MainDashboardService
{
    /**
     * Get main dashboard data with all relationships
     */
    public function getMainDashboard($search = null)
    {
        // Query rooms with all necessary relationships
        $query = Room::query()
            ->with([
                'college:id,college_name',
                'assignedUser:id,username,first_name,last_name',
                'schedules' => function($q) {
                    $q->select('id', 'room_id', 'event_title', 'course_name', 'day_of_week', 'start_time', 'end_time', 'date')
                      ->where('date', '>=', now()->format('Y-m-d'))
                      ->orderBy('date')
                      ->orderBy('start_time')
                      ->limit(3);
                },
                'building:id,building_name'
            ])
            ->select('rooms.*')
            ->latest();

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rooms.room_name', 'like', "%{$search}%")
                  ->orWhere('rooms.location', 'like', "%{$search}%")
                  ->orWhere('rooms.room_code', 'like', "%{$search}%")
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assignedUser', function ($q) use ($search) {
                      $q->where('username', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('schedules', function ($q) use ($search) {
                      $q->where('event_title', 'like', "%{$search}%")
                        ->orWhere('course_name', 'like', "%{$search}%")
                        ->orWhere('day_of_week', 'like', "%{$search}%");
                  });
            });
        }

        // Return paginated results (10 items per page)
        return $query->paginate(10)->withQueryString();
    }

    /**
     * Get dashboard statistics from database
     */
    public function getDashboardStats()
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
                'activeSchedules' => Schedule::where('status', 'approved')->where('date', '>=', now()->format('Y-m-d'))->count(),
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

    /**
     * Get additional dashboard data
     */
    public function getAdditionalDashboardData($search = null)
    {
        return [
            'recentRooms' => Room::with(['college:id,college_name'])
                ->latest()
                ->take(5)
                ->get(),
            'activeUsers' => UserAccount::where('account_status', 'active')
                ->latest()
                ->take(5)
                ->get(),
            'upcomingSchedules' => Schedule::with(['room:id,room_name', 'faculty:id,first_name,last_name'])
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where('status', 'approved')
                ->orderBy('date')
                ->orderBy('start_time')
                ->take(5)
                ->get(),
        ];
    }
}

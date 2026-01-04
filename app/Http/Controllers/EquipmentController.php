<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Building;
use App\Models\Equipment;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Display the equipment management page
     */
    public function index()
    {
        // Return JSON response for now (for testing)
        $equipment = Equipment::with(['room', 'building', 'college', 'department', 'assignedUser'])
            ->orderBy('equipment_name')
            ->paginate(20);

        $rooms = Room::all();
        $buildings = Building::all();
        $colleges = College::all();
        $departments = Department::all();
        $users = UserAccount::whereIn('user_type', ['faculty', 'staff'])->get();

        return Inertia::render('equipment',[
            'equipment' => $equipment,
            'rooms' => $rooms,
            'buildings' => $buildings,
            'colleges' => $colleges,
            'departments' => $departments,
            'users' => $users,
            'stats' => $this->getEquipmentStats(),
        ]);
    }

    /**
     * Get equipment statistics for charts
     */
    public function getEquipmentStats()
    {
        $total = Equipment::count();
        $available = Equipment::where('status', 'available')->count();
        $inUse = Equipment::where('status', 'in_use')->count();
        $maintenance = Equipment::where('status', 'maintenance')->count();
        $retired = Equipment::where('status', 'retired')->count();

        $totalValue = Equipment::sum('purchase_price');
        $avgValue = $total > 0 ? $totalValue / $total : 0;

        // Equipment by person (assigned user)
        $personStats = Equipment::select('assigned_user_id', DB::raw('COUNT(*) as equipment_count'))
            ->whereNotNull('assigned_user_id')
            ->with(['assignedUser:id,first_name,last_name'])
            ->groupBy('assigned_user_id')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->assignedUser ? $item->assignedUser->first_name . ' ' . $item->assignedUser->last_name : 'Unassigned',
                    'equipmentCount' => $item->equipment_count
                ];
            });

        // Equipment by building
        $buildingStats = Equipment::select('building_id', DB::raw('COUNT(*) as equipment_count'))
            ->whereNotNull('building_id')
            ->with(['building:id,building_name'])
            ->groupBy('building_id')
            ->get()
            ->map(function($item) {
                return [
                    'building' => $item->building ? $item->building->building_name : 'Unknown',
                    'equipmentCount' => $item->equipment_count
                ];
            });

        return response()->json([
            'total' => $total,
            'available' => $available,
            'in_use' => $inUse,
            'maintenance' => $maintenance,
            'retired' => $retired,
            'total_value' => $totalValue,
            'average_value' => round($avgValue, 2),
            'person_stats' => $personStats,
            'building_stats' => $buildingStats,
        ]);
    }

    /**
     * Get equipment usage details for Vue components
     */
    public function getEquipmentUsage(Request $request)
    {
        $search = $request->get('search', '');

        $query = Equipment::with([
            'room:id,room_name,room_code',
            'building:id,building_name',
            'college:id,college_name',
            'assignedUser:id,first_name,last_name,middle_name,username'
        ])
        ->whereNotNull('assigned_user_id');

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('inventory_id', 'like', "%{$search}%")
                  ->orWhere('property_id', 'like', "%{$search}%")
                  ->orWhereHas('assignedUser', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function($q) use ($search) {
                      $q->where('room_code', 'like', "%{$search}%")
                        ->orWhere('room_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('building', function($q) use ($search) {
                      $q->where('building_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('college', function($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  });
            });
        }

        // Group by assigned user to get aggregated view
        $equipmentByUser = $query->get()
            ->groupBy('assigned_user_id')
            ->map(function($equipments, $userId) {
                $user = $equipments->first()->assignedUser;
                $room = $equipments->first()->room;
                $building = $equipments->first()->building;
                $college = $equipments->first()->college;

                return [
                    'id' => $userId,
                    'name' => $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User',
                    'room' => $room ? $room->room_code : 'N/A',
                    'building' => $building ? $building->building_name : 'N/A',
                    'college' => $college ? $college->college_name : 'N/A',
                    'equipmentUsed' => $equipments->map(function($eq) {
                        return [
                            'inventory_id' => $eq->inventory_id,
                            'property_id' => $eq->property_id,
                            'name' => $eq->equipment_name,
                            'cfic' => $eq->cfic_id,
                            'status' => ucfirst(str_replace('_', ' ', $eq->status)),
                            'description' => $eq->description
                        ];
                    })->toArray()
                ];
            })
            ->values();

        return response()->json([
            'usage_list' => $equipmentByUser,
            'total' => $equipmentByUser->count(),
            'total_equipment' => $equipmentByUser->sum(function($user) {
                return count($user['equipmentUsed']);
            })
        ]);
    }

    /**
     * Get all equipment with filters
     */
    public function getAll(Request $request)
    {
        $query = Equipment::with(['room', 'building', 'college', 'department', 'assignedUser']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('inventory_id', 'like', "%{$search}%")
                  ->orWhere('property_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('cfic_id')) {
            $query->where('cfic_id', $request->cfic_id);
        }

        $sortField = $request->get('sort_field', 'equipment_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    // ... keep your existing store, update, destroy, transfer methods as they are
}

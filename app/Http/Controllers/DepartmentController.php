<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\College;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::with(['college', 'head'])
            ->orderBy('department_name')
            ->paginate(20);

        $colleges = College::all();
        $heads = UserAccount::whereIn('user_type', ['faculty', 'staff', 'department_head'])
            ->orWhere('roles', 'like', '%department_head%')
            ->get();

        return response()->json([
            'departments' => $departments,
            'colleges' => $colleges,
            'heads' => $heads,
            'stats' => [
                'total' => Department::count(),
                'with_head' => Department::whereNotNull('department_head_id')->count(),
                'avg_rooms' => round(Department::withCount('rooms')->get()->avg('rooms_count'), 2),
                'avg_equipment' => round(Department::withCount('equipment')->get()->avg('equipment_count'), 2),
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        $query = Department::with(['college', 'head']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('department_name', 'like', "%{$search}%")
                  ->orWhere('department_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('has_head')) {
            if (filter_var($request->has_head, FILTER_VALIDATE_BOOLEAN)) {
                $query->whereNotNull('department_head_id');
            } else {
                $query->whereNull('department_head_id');
            }
        }

        $sortField = $request->get('sort_field', 'department_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats(Department $department)
    {
        $roomsCount = $department->rooms()->count();
        $equipmentCount = $department->equipment()->count();
        $usersCount = $department->userAccounts()->count();

        return response()->json([
            'department' => $department->load(['college', 'head']),
            'stats' => [
                'rooms' => $roomsCount,
                'equipment' => $equipmentCount,
                'users' => $usersCount,
                'available_rooms' => $department->rooms()->where('status', 'available')->count(),
                'available_equipment' => $department->equipment()->where('status', 'available')->count(),
                'room_types' => DB::table('rooms')
                    ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                    ->where('rooms.department_id', $department->id)
                    ->select('room_types.room_type_name', DB::raw('COUNT(*) as count'))
                    ->groupBy('room_types.room_type_name')
                    ->get(),
            ],
            'recent_activities' => $this->getDepartmentActivities($department),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:50|unique:departments,department_code',
            'college_id' => 'required|exists:colleges,id',
            'department_head_id' => 'nullable|exists:user_accounts,id',
            'description' => 'nullable|string',
            'office_location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'message' => 'Department created successfully',
            'department' => $department->load(['college', 'head']),
        ], 201);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_name' => 'sometimes|required|string|max:255',
            'department_code' => 'sometimes|required|string|max:50|unique:departments,department_code,' . $department->id,
            'college_id' => 'sometimes|required|exists:colleges,id',
            'department_head_id' => 'nullable|exists:user_accounts,id',
            'description' => 'nullable|string',
            'office_location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $department->update($validated);

        return response()->json([
            'message' => 'Department updated successfully',
            'department' => $department->load(['college', 'head']),
        ]);
    }

    public function destroy(Department $department)
    {
        // Check if department has dependencies
        if ($department->rooms()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with existing rooms'
            ], 422);
        }

        if ($department->equipment()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with existing equipment'
            ], 422);
        }

        if ($department->userAccounts()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with existing users'
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }

    private function getDepartmentActivities(Department $department)
    {
        $activities = [];

        // Recent room changes
        $recentRooms = $department->rooms()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRooms as $room) {
            $activities[] = [
                'type' => 'room',
                'action' => 'updated',
                'description' => "Room {$room->room_name} was updated",
                'time' => $room->updated_at->diffForHumans(),
            ];
        }

        // Recent equipment changes
        $recentEquipment = $department->equipment()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentEquipment as $equipment) {
            $activities[] = [
                'type' => 'equipment',
                'action' => 'updated',
                'description' => "Equipment {$equipment->equipment_name} was updated",
                'time' => $equipment->updated_at->diffForHumans(),
            ];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }
}

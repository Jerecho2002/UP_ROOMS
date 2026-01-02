<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function index(Request $request)
    {
        $users = UserAccount::with(['college', 'department'])
            ->orderBy('last_name')
            ->paginate(20);

        $colleges = College::all();
        $departments = Department::all();

        return response()->json([
            'users' => $users,
            'colleges' => $colleges,
            'departments' => $departments,
            'stats' => $this->getUserStats(),
        ]);
    }

    public function getAll(Request $request)
    {
        $query = UserAccount::with(['college', 'department']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->has('account_status')) {
            $query->where('account_status', $request->account_status);
        }

        $sortField = $request->get('sort_field', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats()
    {
        $total = UserAccount::count();
        $active = UserAccount::where('account_status', 'active')->count();
        $inactive = UserAccount::where('account_status', 'inactive')->count();
        $suspended = UserAccount::where('account_status', 'suspended')->count();

        $byUserType = UserAccount::select('user_type', DB::raw('COUNT(*) as count'))
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type')
            ->toArray();

        $recentLogins = UserAccount::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->limit(5)
            ->get(['id', 'username', 'full_name', 'last_login_at']);

        $recentRegistrations = UserAccount::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'username', 'full_name', 'created_at']);

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'suspended' => $suspended,
            'by_user_type' => $byUserType,
            'recent_logins' => $recentLogins,
            'recent_registrations' => $recentRegistrations,
        ];
    }

    public function getUserStats($id)
    {
        $user = UserAccount::with(['college', 'department'])->findOrFail($id);

        $assignedRoomsCount = $user->assignedRooms()->count();
        $assignedEquipmentCount = $user->assignedEquipment()->count();
        $schedulesCount = $user->schedules()->count();
        $requestedSchedulesCount = $user->requestedSchedules()->count();

        $recentActivities = [];

        // Recent room assignments
        $recentRooms = $user->assignedRooms()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRooms as $room) {
            $recentActivities[] = [
                'type' => 'room_assignment',
                'action' => 'assigned',
                'description' => "Assigned to room {$room->room_name}",
                'time' => $room->updated_at->diffForHumans(),
            ];
        }

        // Recent equipment assignments
        $recentEquipment = $user->assignedEquipment()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentEquipment as $equipment) {
            $recentActivities[] = [
                'type' => 'equipment_assignment',
                'action' => 'assigned',
                'description' => "Assigned equipment {$equipment->equipment_name}",
                'time' => $equipment->updated_at->diffForHumans(),
            ];
        }

        // Recent schedule requests
        $recentSchedules = $user->requestedSchedules()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentSchedules as $schedule) {
            $recentActivities[] = [
                'type' => 'schedule_request',
                'action' => $schedule->status,
                'description' => "Requested schedule for {$schedule->event_title}",
                'time' => $schedule->created_at->diffForHumans(),
            ];
        }

        usort($recentActivities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return response()->json([
            'user' => $user,
            'stats' => [
                'assigned_rooms' => $assignedRoomsCount,
                'assigned_equipment' => $assignedEquipmentCount,
                'schedules' => $schedulesCount,
                'requested_schedules' => $requestedSchedulesCount,
                'days_since_last_login' => $user->last_login_at ?
                    \Carbon\Carbon::parse($user->last_login_at)->diffInDays(now()) : null,
                'account_age' => $user->created_at->diffInDays(now()),
            ],
            'recent_activities' => array_slice($recentActivities, 0, 10),
        ]);
    }

    public function changeStatus($id, Request $request)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update([
            'account_status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user->load(['college', 'department']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:user_accounts,username',
            'email' => 'required|email|max:255|unique:user_accounts,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'employee_id' => 'required|string|max:50|unique:user_accounts,employee_id',
            'profile_picture' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'user_type' => 'required|in:admin,faculty,staff,student,dean,department_head',
            'roles' => 'nullable|array',
            'account_status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = UserAccount::create($validated);

        return response()->json([
            'message' => 'User account created successfully',
            'user' => $user->load(['college', 'department']),
        ], 201);
    }

    public function update(Request $request, UserAccount $userAccount)
    {
        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:50|unique:user_accounts,username,' . $userAccount->id,
            'email' => 'sometimes|required|email|max:255|unique:user_accounts,email,' . $userAccount->id,
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'employee_id' => 'sometimes|required|string|max:50|unique:user_accounts,employee_id,' . $userAccount->id,
            'profile_picture' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'user_type' => 'sometimes|required|in:admin,faculty,staff,student,dean,department_head',
            'roles' => 'nullable|array',
            'account_status' => 'sometimes|required|in:active,inactive,suspended',
        ]);

        $userAccount->update($validated);

        return response()->json([
            'message' => 'User account updated successfully',
            'user' => $userAccount->load(['college', 'department']),
        ]);
    }

    public function updateProfile($id, Request $request)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load(['college', 'department']),
        ]);
    }

    public function changePassword($id, Request $request)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    public function destroy(UserAccount $userAccount)
    {
        // Check if user has dependencies
        if ($userAccount->deanCollege()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user who is assigned as dean'
            ], 422);
        }

        if ($userAccount->headDepartment()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user who is assigned as department head'
            ], 422);
        }

        if ($userAccount->assignedRooms()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user with room assignments'
            ], 422);
        }

        if ($userAccount->assignedEquipment()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user with equipment assignments'
            ], 422);
        }

        $userAccount->delete();

        return response()->json([
            'message' => 'User account deleted successfully'
        ]);
    }
}

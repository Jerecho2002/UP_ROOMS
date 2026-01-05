<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserAccountController extends Controller
{
    // Render the user account management page using Inertia
    public function index(Request $request)
    {
        // Get paginated users with relationships
        $users = UserAccount::with(['college', 'department'])
            ->orderBy('last_name')
            ->paginate(20);

        // Get colleges and departments for dropdowns
        $colleges = College::all();
        $departments = Department::all();

        // Get stats
        $stats = $this->getStats();

        return Inertia::render('UserAccount/Index', [
            'users' => $users,
            'colleges' => $colleges,
            'departments' => $departments,
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search', ''),
                'college_id' => $request->input('college_id', null),
                'department_id' => $request->input('department_id', null),
                'user_type' => $request->input('user_type', null),
                'account_status' => $request->input('account_status', null),
            ]
        ]);
    }

    // Get stats for the dashboard
    private function getStats()
    {
        return [
            'total' => UserAccount::count(),
            'active' => UserAccount::where('account_status', 'active')->count(),
            'inactive' => UserAccount::where('account_status', 'inactive')->count(),
            'suspended' => UserAccount::where('account_status', 'suspended')->count(),
            'pending' => UserAccount::where('account_status', 'pending')->count(),
            'by_user_type' => UserAccount::select('user_type', DB::raw('COUNT(*) as count'))
                ->groupBy('user_type')
                ->get()
                ->pluck('count', 'user_type')
                ->toArray(),
        ];
    }

    // Store new user
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
            'user_type' => 'required|in:admin,faculty,staff,student,guest',
            'roles' => 'nullable|array',
            'account_status' => 'required|in:active,inactive,suspended,pending',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = UserAccount::create($validated);

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account created successfully!');
    }

    // Update user
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
            'user_type' => 'sometimes|required|in:admin,faculty,staff,student,guest',
            'roles' => 'nullable|array',
            'account_status' => 'sometimes|required|in:active,inactive,suspended,pending',
        ]);

        $userAccount->update($validated);

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account updated successfully!');
    }

    // Delete user
    public function destroy(UserAccount $userAccount)
    {
        // Check if user has dependencies
        if ($userAccount->deanCollege()->count() > 0) {
            return back()->with('error', 'Cannot delete user who is assigned as dean');
        }

        if ($userAccount->headDepartment()->count() > 0) {
            return back()->with('error', 'Cannot delete user who is assigned as department head');
        }

        if ($userAccount->assignedRooms()->count() > 0) {
            return back()->with('error', 'Cannot delete user with room assignments');
        }

        if ($userAccount->assignedEquipment()->count() > 0) {
            return back()->with('error', 'Cannot delete user with equipment assignments');
        }

        $userAccount->delete();

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account deleted successfully!');
    }

    // Change user status
    public function changeStatus(UserAccount $userAccount, Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,pending',
            'reason' => 'nullable|string|max:500',
        ]);

        $userAccount->update([
            'account_status' => $request->status,
        ]);

        return redirect()->route('user-accounts.index')
            ->with('success', 'User status updated successfully!');
    }

    // API endpoint for Vue component
    public function apiIndex(Request $request)
    {
        $query = UserAccount::with(['college', 'department']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Filter by college
        if ($request->has('college_id') && $request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by user type
        if ($request->has('user_type') && $request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by account status
        if ($request->has('account_status') && $request->account_status) {
            $query->where('account_status', $request->account_status);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');

        // Handle special sorting for relationships
        if ($sortField === 'college') {
            $query->join('colleges', 'user_accounts.college_id', '=', 'colleges.id')
                  ->orderBy('colleges.name', $sortOrder)
                  ->select('user_accounts.*');
        } elseif ($sortField === 'department') {
            $query->join('departments', 'user_accounts.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortOrder)
                  ->select('user_accounts.*');
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'stats' => $this->getStats(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('UserAccountPage');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = UserAccount::with(['college:id,college_name', 'department:id,department_name'])
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('user_type', 'like', "%{$search}%")
                  ->orWhere('account_status', 'like', "%{$search}%")
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('department_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:user_accounts',
            'email' => 'required|email|max:255|unique:user_accounts',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:100|unique:user_accounts',
            'profile_picture' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'user_type' => 'required|in:admin,faculty,staff,student,guest',
            'roles' => 'nullable|array',
            'account_status' => 'required|in:active,inactive,suspended,pending',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = UserAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User account created successfully',
            'data' => $user->load(['college', 'department'])
        ], 201);
    }

    public function update(Request $request, UserAccount $userAccount)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:user_accounts,username,' . $userAccount->id,
            'email' => 'required|email|max:255|unique:user_accounts,email,' . $userAccount->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:100|unique:user_accounts,employee_id,' . $userAccount->id,
            'profile_picture' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'user_type' => 'required|in:admin,faculty,staff,student,guest',
            'roles' => 'nullable|array',
            'account_status' => 'required|in:active,inactive,suspended,pending',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $userAccount->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User account updated successfully',
            'data' => $userAccount->load(['college', 'department'])
        ]);
    }

    public function destroy(UserAccount $userAccount)
    {
        $userAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'User account deleted successfully'
        ]);
    }
}

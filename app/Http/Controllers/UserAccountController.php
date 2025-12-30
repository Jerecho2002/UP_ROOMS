<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Inertia\Inertia; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserAccountController extends Controller
{
    /**
     * Display the User Account Page
     */
    public function indexPage()
    {
        $users = UserAccount::all();

        return Inertia::render('UserAccountPage', [
            'initialUsers' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'role' => $user->role,
                    'department' => $user->department,
                    'college' => $user->college,
                    'permissions' => $user->permissions ?: [],
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            })->toArray()
        ]);
    }

    /**
     * API: Get all users
     */
    public function index()
    {
        $users = UserAccount::all();
        return response()->json(['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:user_accounts'],
            'email' => ['nullable', 'string', 'email', 'max:100', 'unique:user_accounts'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:100'],
            'college' => ['nullable', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
        ]);

        // Handle password hashing
        $validated['password'] = Hash::make($validated['password']);

        // Handle permissions JSON
        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }

        $user = UserAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'username' => ['sometimes', 'string', 'max:50', 'unique:user_accounts,username,' . $id],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:100', 'unique:user_accounts,email,' . $id],
            'first_name' => ['sometimes', 'nullable', 'string', 'max:50'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:50'],
            'role' => ['sometimes', 'string', 'max:50'],
            'department' => ['sometimes', 'nullable', 'string', 'max:100'],
            'college' => ['sometimes', 'nullable', 'string', 'max:150'],
            'permissions' => ['sometimes', 'nullable', 'array'],
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'min:6'],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Handle permissions JSON
        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = UserAccount::findOrFail($id);
        $userName = $user->username;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
            'username' => $userName
        ]);
    }
}

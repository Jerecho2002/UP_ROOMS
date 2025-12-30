<?php

namespace App\Services;

use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class UserAccountService
{
    public function getAllUsers()
    {
        return UserAccount::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'department' => $user->department,
                'college' => $user->college,
                'permissions' => $user->permissions,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
        })->toArray();
    }

    public function searchUsers($query)
    {
        return UserAccount::where('username', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('role', 'like', "%{$query}%")
            ->orWhere('department', 'like', "%{$query}%")
            ->orWhere('college', 'like', "%{$query}%")
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'role' => $user->role,
                    'department' => $user->department,
                    'college' => $user->college,
                    'permissions' => $user->permissions,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ];
            })->toArray();
    }

    public function getDatabaseStats()
    {
        return [
            'total_users' => UserAccount::count(),
            'by_role' => UserAccount::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get()
                ->pluck('count', 'role')
                ->toArray(),
            'by_college' => UserAccount::select('college', DB::raw('count(*) as count'))
                ->groupBy('college')
                ->get()
                ->pluck('count', 'college')
                ->toArray(),
            'recently_added' => UserAccount::orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return $user->username;
                })
                ->toArray()
        ];
    }
}

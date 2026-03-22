<?php

namespace App\Services;

use App\Models\UserAccount;

class UserAccountService
{
    public function getUserAccount(int $perPage = 10, ?string $search = null)
    {
        return UserAccount::with('college', 'department', 'user')
            ->orderByDesc('created_at')
            ->when($search, fn($query) => $query->where('username', 'like', "%{$search}%"))
            ->paginate($perPage)
            ->withQueryString();
    }
}

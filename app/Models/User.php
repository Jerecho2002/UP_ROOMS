<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'role',
        'department',
        'college',
        'permissions'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    // Role check methods
    public function isAdmin()
    {
        return in_array($this->role, ['Admin', 'SYSADMIN']);
    }

    public function isStaff()
    {
        return $this->role === 'Staff';
    }

    public function isFaculty()
    {
        return $this->role === 'Faculty';
    }

    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }
}

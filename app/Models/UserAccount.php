<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'email',
        'first_name',
        'last_name',
        'role',
        'department',
        'college',
        'permissions', // Make sure this is in fillable
        // ... other fields
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array', // Add this line to cast permissions to array/JSON
    ];
}

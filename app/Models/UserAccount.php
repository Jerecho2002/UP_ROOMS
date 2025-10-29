<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAccount extends Authenticatable
{
    use HasFactory;

    protected $table = 'user_accounts';
    protected $fillable = [
        'username',
        'password',
        'email',
        'first_name',
        'last_name',
        'role',
    ];

    public $timestamps = false;

    // 🔗 Relations
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

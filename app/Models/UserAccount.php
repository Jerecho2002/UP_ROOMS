<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAccount extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * Overridden because the table is named 'user_account' instead of 'user_accounts'.
     *
     * @var string
     */
    protected $table = 'user_account';

    /**
     * The primary key for the model.
     * Overridden because the primary key is named 'user_id' instead of 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * Get the rooms created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdRooms(): HasMany
    {
        // 'created_by' is the foreign key on the 'rooms' table, referring to 'user_id'
        return $this->hasMany(Room::class, 'created_by', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class College extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'colleges';

    // Custom primary key
    protected $primaryKey = 'college_id';

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [];

    /**
     * Get the Departments associated with the College.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'college_id', 'college_id');
    }

    /**
     * Get the Rooms belonging to the College.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'college_id', 'college_id');
    }

    /**
     * Get the Equipment associated with this College.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'college_id', 'college_id');
    }
}

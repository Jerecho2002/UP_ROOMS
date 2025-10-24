<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'room_types';

    // Custom primary key
    protected $primaryKey = 'room_type_id';

    // Disable default timestamps since they are not in the migration for this table
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [];

    /**
     * Get the Rooms that are of this type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_type_id', 'room_type_id');
    }

    /**
     * Get the Equipment associated with this Room Type.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'room_type_id', 'room_type_id');
    }
}

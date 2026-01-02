<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'room_code',
        'building_id',
        'college_id',
        'department_id',
        'room_type_id',
        'assigned_user_id',
        'floor_number',
        'location',
        'capacity',
        'area_sqm',
        'facilities',
        'status',
        'notes',
    ];

    protected $casts = [
        'facilities' => 'json',
        'status' => 'string',
    ];

    // Relationships
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(UserAccount::class, 'assigned_user_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'room_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'room_id');
    }
}

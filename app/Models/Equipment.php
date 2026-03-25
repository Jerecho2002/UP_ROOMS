<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equipment_name',
        'inventory_id',
        'property_id',
        'description',
        'quantity',
        'cfic_id',
        'serial_number',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_equipment');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(UserAccount::class, 'assigned_user_id');
    }
}

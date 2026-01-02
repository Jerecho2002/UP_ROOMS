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
        'room_id',
        'building_id',
        'college_id',
        'department_id',
        'cfic_id',
        'status',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'assigned_user_id',
        'specifications',
    ];

    protected $casts = [
        'specifications' => 'array',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

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

    public function assignedUser()
    {
        return $this->belongsTo(UserAccount::class, 'assigned_user_id');
    }

    public function schedulesWithCfic()
    {
        return $this->hasMany(Schedule::class, 'cfic_id', 'cfic_id');
    }

    // Helper methods
    public function getFullLocationAttribute()
    {
        $location = [];
        if ($this->room) {
            $location[] = "Room: {$this->room->room_name}";
            if ($this->room->building) {
                $location[] = "Building: {$this->room->building->building_name}";
            }
        }
        if ($this->college) {
            $location[] = "College: {$this->college->college_name}";
        }
        return implode(' | ', $location);
    }

    public function getAccountablePersonAttribute()
    {
        return $this->assignedUser ? $this->assignedUser->full_name : 'Unassigned';
    }
}

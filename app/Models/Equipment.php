<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';
    protected $fillable = [
        'equipment_name',
        'description',
        'quantity',
        'room_type_id',
        'building_id',
        'college_id'
    ];

    // 🔗 Relations
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}

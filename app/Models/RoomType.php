<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $table = 'room_types';
    protected $fillable = ['room_type_name', 'description'];
    public $timestamps = false;

    // 🔗 Relations
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}

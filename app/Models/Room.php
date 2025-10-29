<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';
    protected $fillable = [
        'room_name',
        'building_id',
        'college_id',
        'capacity',
        'location',
        'room_type_id',
        'user_account_id'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}

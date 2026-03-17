<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserAccount extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'user_accounts';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'employee_number',
        'gender',
        'contact_number',
        'college_id',
        'department_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function deanCollege()
    {
        return $this->hasOne(College::class, 'dean_id');
    }

    public function headDepartment()
    {
        return $this->hasOne(Department::class, 'department_head_id');
    }

    public function assignedRooms()
    {
        return $this->hasMany(Room::class, 'assigned_user_id');
    }

    public function assignedEquipment()
    {
        return $this->hasMany(Equipment::class, 'assigned_user_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'faculty_id');
    }

    public function requestedSchedules()
    {
        return $this->hasMany(Schedule::class, 'requester_id');
    }
}

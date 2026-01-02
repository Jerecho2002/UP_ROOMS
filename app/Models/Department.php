<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_name',
        'department_code',
        'college_id',
        'department_head_id',
        'description',
        'office_location',
        'contact_email',
        'contact_phone',
    ];

    // Relationships
    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function head()
    {
        return $this->belongsTo(UserAccount::class, 'department_head_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'department_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'department_id');
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'department_id');
    }
}

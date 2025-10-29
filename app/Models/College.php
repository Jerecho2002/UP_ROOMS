<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $table = 'colleges';
    protected $fillable = ['college_name', 'description'];

    public $timestamps = false;

    // 🔗 Relations
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}

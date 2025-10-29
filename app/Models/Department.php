<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $fillable = ['department_name', 'college_id', 'description'];
    public $timestamps = false;

    // 🔗 Relations
    public function college()
    {
        return $this->belongsTo(College::class);
    }
}

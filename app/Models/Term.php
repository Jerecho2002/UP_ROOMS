<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_name',
        'term_code',
        'term_type',
        'start_date',
        'end_date',
        'enrollment_start',
        'enrollment_end',
        'classes_start',
        'classes_end',
        'examination_start',
        'examination_end',
        'is_current',
        'status',
        'academic_year',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'enrollment_start' => 'date',
        'enrollment_end' => 'date',
        'classes_start' => 'date',
        'classes_end' => 'date',
        'examination_start' => 'date',
        'examination_end' => 'date',
        'is_current' => 'boolean',
        'status' => 'string',
    ];

    // Relationships
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'term_id');
    }
}

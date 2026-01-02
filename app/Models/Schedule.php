<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'event_title',
        'event_type',
        'course_code',
        'course_name',
        'section',
        'faculty_name',
        'faculty_id',
        'date',
        'start_time',
        'end_time',
        'day_of_week',
        'number_of_participants',
        'requester_id',
        'requester_name',
        'description',
        'agenda',
        'organizer',
        'equipment_needed',
        'additional_requirements',
        'status',
        'is_recurring',
        'recurrence_pattern',
        'term_id',
        'cfic_id',
    ];

    protected $casts = [
        'equipment_needed' => 'json',
        'additional_requirements' => 'json',
        'recurrence_pattern' => 'json',
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_recurring' => 'boolean',
        'status' => 'string',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function faculty()
    {
        return $this->belongsTo(UserAccount::class, 'faculty_id');
    }

    public function requester()
    {
        return $this->belongsTo(UserAccount::class, 'requester_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }
}

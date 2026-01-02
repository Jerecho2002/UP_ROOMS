<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'room_id',
        'schedule_id',
        'checkout_date',
        'checkout_time',
        'expected_return_date',
        'expected_return_time',
        'actual_return_date',
        'actual_return_time',
        'usage_status',
        'notes',
        'checkout_by',
        'received_by'
    ];

    protected $casts = [
        'checkout_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'checkout_time' => 'datetime:H:i',
        'expected_return_time' => 'datetime:H:i',
        'actual_return_time' => 'datetime:H:i'
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}

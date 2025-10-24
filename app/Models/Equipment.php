<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'equipment';

    // Custom primary key
    protected $primaryKey = 'equipment_id';

    // Disable default timestamps since they are not in the migration for this table
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [];

    /**
     * Get the Facility the Equipment belongs to (based on facility_id FK).
     * Note: Assumes a Facility model exists, even if the migration was not provided.
     */
    public function facility(): BelongsTo
    {
        // Assuming the Facility model has 'facility_id' as its primary key.
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Get the Building the Equipment belongs to.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id', 'building_id');
    }

    /**
     * Get the College the Equipment belongs to.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }

    /**
     * Get the RoomType the Equipment is associated with.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'room_type_id');
    }
}

// NOTE: I am also including a placeholder for the Facility model because the 'equipment' table
// references 'facilities', which is necessary for the Equipment model's relationships to compile.
class Facility extends Model
{
    // Placeholder model for the 'facilities' table
    protected $table = 'facilities';
    protected $primaryKey = 'facility_id';
    public $timestamps = false;
    protected $guarded = [];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'facility_id', 'facility_id');
    }
}

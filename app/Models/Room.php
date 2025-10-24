<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'room_id';

    /**
     * Indicates if the model should be timestamped.
     * The migration doesn't include $table->timestamps(), so we set this to false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_name',
        'building_id',
        'college_id',
        'capacity',
        'location',
        'room_type_id',
        'created_by',
    ];

    // --- Relationships ---

    /**
     * Get the building that owns the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building(): BelongsTo
    {
        // 'building_id' is the foreign key on the rooms table, referring to 'building_id' on the buildings table
        return $this->belongsTo(Building::class, 'building_id', 'building_id');
    }

    /**
     * Get the college that owns the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function college(): BelongsTo
    {
        // 'college_id' is the foreign key on the rooms table, referring to 'college_id' on the colleges table
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }

    /**
     * Get the type of the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roomType(): BelongsTo
    {
        // 'room_type_id' is the foreign key on the rooms table, referring to 'room_type_id' on the room_types table
        return $this->belongsTo(RoomType::class, 'room_type_id', 'room_type_id');
    }

    /**
     * Get the user who created the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        // 'created_by' is the foreign key on the rooms table, referring to 'user_id' on the user_account table
        return $this->belongsTo(UserAccount::class, 'created_by', 'user_id');
    }
}

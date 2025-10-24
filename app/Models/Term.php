<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'terms';

    // Custom primary key
    protected $primaryKey = 'term_id';

    // Disable default timestamps since they are not in the migration for this table
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [];
}

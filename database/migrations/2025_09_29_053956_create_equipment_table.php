<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
    
            $table->string('equipment_name', 100);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            
            // ✅ Data type should be unsignedBigInteger to match the referenced table's primary key (ID)
            $table->unsignedBigInteger('room_type_id')->nullable();
            
            $table->unsignedBigInteger('property_id')->nullable(); 

            // Foreign key for room_type_id - looks correct assuming room_types exists
            $table->foreign('room_type_id')->references('room_type_id')->on('room_types')->onDelete('set null');
            
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
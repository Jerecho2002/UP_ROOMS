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
       Schema::create('equipments', function (Blueprint $table) {
            $table->id('id');
            $table->string('equipment_name', 100);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable(); 

            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');

            $table->timestamps();
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
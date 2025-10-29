<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('equipment', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->string('equipment_name', 100);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->integer('property_id')->nullable(); // ✅ new column

            // ✅ removed connection to user_account
            // $table->unsignedBigInteger('created_by')->nullable();
            // $table->foreign('created_by')->references('user_id')->on('user_account')->onDelete('set null');

            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

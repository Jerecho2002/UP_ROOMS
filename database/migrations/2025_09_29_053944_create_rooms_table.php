<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->string('room_name', 50);
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('location', 100)->nullable();
            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('building_id')->references('building_id')->on('buildings')->onDelete('set null');
            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('set null');
            $table->foreign('room_type_id')->references('room_type_id')->on('room_types')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('user_account')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

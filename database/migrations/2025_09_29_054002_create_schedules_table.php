<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
          Schema::create('schedules', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('course_name', 100);
            $table->string('day', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('cfic_id', 100)->nullable();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

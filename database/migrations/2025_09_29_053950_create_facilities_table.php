<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('facilities', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('facility_name', 100);
            $table->text('description')->nullable();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};

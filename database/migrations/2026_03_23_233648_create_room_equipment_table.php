<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_equipment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();

            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['room_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_equipment');
    }
};

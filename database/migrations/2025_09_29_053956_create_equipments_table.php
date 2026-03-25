<?php
// database/migrations/xxxx_xx_xx_create_equipment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_name', 100);
            $table->string('inventory_id', 50)->unique();
            $table->string('property_id', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('cfic_id', 100)->nullable();
            $table->string('serial_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

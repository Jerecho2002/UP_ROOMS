<?php

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
            $table->string('property_id', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('room_id')->nullable(); // Will be foreign key later
            $table->unsignedBigInteger('building_id')->nullable(); // Will be foreign key later
            $table->unsignedBigInteger('college_id')->nullable(); // Will be foreign key later
            $table->unsignedBigInteger('department_id')->nullable(); // Will be foreign key later
            $table->string('cfic_id', 100)->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'damaged', 'retired'])->default('available');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable(); // Will be foreign key later
            $table->json('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // NO FOREIGN KEYS HERE - add later
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

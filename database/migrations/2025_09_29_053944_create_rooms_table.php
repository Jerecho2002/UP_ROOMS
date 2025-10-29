<?php

// create_rooms_table.php (No changes needed, looks correct)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('rooms', function (Blueprint $table) {
            $table->id('id');
            $table->string('room_name', 50);
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('location', 100)->nullable();
            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->unsignedBigInteger('user_account_id')->nullable();

            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
            $table->foreign('user_account_id')->references('id')->on('user_accounts')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
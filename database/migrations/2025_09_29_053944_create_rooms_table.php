<?php

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
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('user_accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

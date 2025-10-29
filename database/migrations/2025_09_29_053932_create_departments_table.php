<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id('id');
            $table->string('department_name', 150);
            $table->unsignedBigInteger('college_id')->nullable();
            $table->text('description')->nullable();

            $table->foreign('college_id')
                ->references('id')->on('colleges')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

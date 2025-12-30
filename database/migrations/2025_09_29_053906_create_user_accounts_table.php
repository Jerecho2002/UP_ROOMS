<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('email', 100)->unique()->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('role', 50)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('college', 150)->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Optional: for soft deletion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add foreign keys to user_accounts
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // 2. Add foreign keys to colleges
        Schema::table('colleges', function (Blueprint $table) {
            $table->foreign('dean_id')->references('id')->on('user_accounts')->onDelete('set null');
        });

        // 3. Add foreign keys to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('department_head_id')->references('id')->on('user_accounts')->onDelete('set null');
        });

        // 4. Add foreign keys to buildings
        Schema::table('buildings', function (Blueprint $table) {
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
        });

        // 5. Add foreign keys to rooms
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('set null');
            $table->foreign('assigned_user_id')->references('id')->on('user_accounts')->onDelete('set null');
        });

        // 6. Add foreign keys to equipment
        Schema::table('equipment', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('assigned_user_id')->references('id')->on('user_accounts')->onDelete('set null');
        });

        // 7. Add foreign keys to schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('user_accounts')->onDelete('set null');
            $table->foreign('requester_id')->references('id')->on('user_accounts')->onDelete('set null');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop all foreign keys in reverse order

        // 7. Drop foreign keys from schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['requester_id']);
            $table->dropForeign(['term_id']);
        });

        // 6. Drop foreign keys from equipment
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['building_id']);
            $table->dropForeign(['college_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['assigned_user_id']);
        });

        // 5. Drop foreign keys from rooms
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropForeign(['college_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['room_type_id']);
            $table->dropForeign(['assigned_user_id']);
        });

        // 4. Drop foreign keys from buildings
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign(['college_id']);
        });

        // 3. Drop foreign keys from departments
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['college_id']);
            $table->dropForeign(['department_head_id']);
        });

        // 2. Drop foreign keys from colleges
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropForeign(['dean_id']);
        });

        // 1. Drop foreign keys from user_accounts
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropForeign(['college_id']);
            $table->dropForeign(['department_id']);
        });
    }
};

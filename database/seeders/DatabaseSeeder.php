<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\UserAccount::factory(10)->create();
        \App\Models\Building::factory(5)->create();
        \App\Models\College::factory(4)->create();
        \App\Models\Department::factory(6)->create();
        \App\Models\RoomType::factory(4)->create();
        \App\Models\Room::factory(10)->create();
        \App\Models\Equipment::factory(20)->create();
        \App\Models\Schedule::factory(15)->create();
        \App\Models\Term::factory(3)->create();
    }

}

<?php

namespace Database\Seeders;

use App\Models\UserAccount;
use App\Models\College;
use App\Models\Department;
use App\Models\Building;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Equipment;
use App\Models\Term;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default admin user
        $this->command->info('Creating admin user...');
        $admin = UserAccount::create([
            'username' => 'admin',
            'email' => 'admin@upcebu.edu.ph',
            'password' => Hash::make('password123'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'user_type' => 'admin',
            'account_status' => 'active',
            'roles' => ['admin', 'super_admin'],
        ]);

        // Create colleges
        $this->command->info('Creating colleges...');
        $colleges = College::factory()->count(4)->create();

        // Set admin as dean for first college
        $colleges[0]->update(['dean_id' => $admin->id]);

        // Create departments
        $this->command->info('Creating departments...');
        $departments = Department::factory()->count(6)->create();

        // Create additional users
        $this->command->info('Creating additional users...');
        $users = UserAccount::factory()->count(9)->create();

        // Assign some users as department heads
        foreach ($departments->take(3) as $index => $department) {
            $department->update(['department_head_id' => $users[$index]->id]);
        }

        // Create buildings
        $this->command->info('Creating buildings...');
        $buildings = Building::factory()->count(5)->create();

        // Create room types
        $this->command->info('Creating room types...');
        $roomTypes = RoomType::factory()->count(4)->create();

        // Create rooms
        $this->command->info('Creating rooms...');
        $rooms = Room::factory()->count(10)->create();

        // Create equipment
        $this->command->info('Creating equipment...');
        $equipment = Equipment::factory()->count(20)->create();

        // Create terms
        $this->command->info('Creating terms...');
        $terms = Term::factory()->count(3)->create();

        // Make one term current
        if ($terms->isNotEmpty()) {
            $terms->first()->update(['is_current' => true, 'status' => 'active']);
        }

        $this->command->info('Database seeded successfully!');

        // Show login credentials
        $this->command->info('');
        $this->command->info('===========================');
        $this->command->info('LOGIN CREDENTIALS:');
        $this->command->info('Username: admin');
        $this->command->info('Password: password123');
        $this->command->info('===========================');
    }
}

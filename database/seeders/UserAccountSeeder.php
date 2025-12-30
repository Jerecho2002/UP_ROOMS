<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data using delete() instead of truncate()
        UserAccount::query()->delete();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create sample users
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'email' => 'admin@upcebu.edu.ph',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'Admin',
                'department' => 'N/A - Administration',
                'college' => 'College of Information Technology (CIT)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'])
            ],
            [
                'username' => 'jdelacruz',
                'password' => Hash::make('password123'),
                'email' => 'juan.delacruz@upcebu.edu.ph',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'role' => 'Faculty',
                'department' => 'Computer Science',
                'college' => 'College of Information Technology (CIT)',
                'permissions' => json_encode(['Can Book', 'User Type Only'])
            ],
            [
                'username' => 'msantos',
                'password' => Hash::make('password123'),
                'email' => 'maria.santos@upcebu.edu.ph',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'role' => 'Staff',
                'department' => 'Management',
                'college' => 'College of Business and Accountancy (CBA)',
                'permissions' => json_encode(['Can Book', 'Staff Work'])
            ],
            [
                'username' => 'rreyes',
                'password' => Hash::make('password123'),
                'email' => 'robert.reyes@upcebu.edu.ph',
                'first_name' => 'Robert',
                'last_name' => 'Reyes',
                'role' => 'DPTAPR',
                'department' => 'Electrical Engineering',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Book', 'User Type Only'])
            ],
            [
                'username' => 'ggarcia',
                'password' => Hash::make('password123'),
                'email' => 'gina.garcia@upcebu.edu.ph',
                'first_name' => 'Gina',
                'last_name' => 'Garcia',
                'role' => 'AO',
                'department' => 'Accounting',
                'college' => 'College of Business and Accountancy (CBA)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Staff Work'])
            ],
        ];

        foreach ($users as $user) {
            UserAccount::create($user);
        }

        // Generate additional fake users using factory
        \App\Models\UserAccount::factory(20)->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@upcebu.edu.ph',
                'password' => Hash::make('password'),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'role' => 'Admin',
                'department' => 'Administration',
                'college' => 'University Administration',
                'permissions' => ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
            ],
            [
                'username' => 'staff',
                'email' => 'staff@upcebu.edu.ph',
                'password' => Hash::make('password'),
                'first_name' => 'John',
                'last_name' => 'Staff',
                'role' => 'Staff',
                'department' => 'Registrar',
                'college' => 'College of Arts and Sciences',
                'permissions' => ['Can Book', 'Staff Work'],
            ],
            [
                'username' => 'faculty',
                'email' => 'faculty@upcebu.edu.ph',
                'password' => Hash::make('password'),
                'first_name' => 'Jane',
                'last_name' => 'Professor',
                'role' => 'Faculty',
                'department' => 'Computer Science',
                'college' => 'College of Information Technology',
                'permissions' => ['Can Book', 'User Type Only'],
            ],
            [
                'username' => 'sysadmin',
                'email' => 'sysadmin@upcebu.edu.ph',
                'password' => Hash::make('password'),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'SYSADMIN',
                'department' => 'IT Department',
                'college' => 'University Administration',
                'permissions' => ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        // Demo users
        User::factory()->count(35)->create();
    }
}

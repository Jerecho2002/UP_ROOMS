<?php

namespace Database\Seeders;

use App\Models\UserAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'email' => 'admin@upcebu.edu.ph',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'Admin',
                'department' => 'N/A - Administration',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'])
            ],
            [
                'username' => 'staff',
                'password' => Hash::make('password123'),
                'email' => 'staff@upcebu.edu.ph',
                'first_name' => 'John',
                'last_name' => 'Staff',
                'role' => 'Staff',
                'department' => 'Computer Science',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Book', 'Staff Work'])
            ],
            [
                'username' => 'faculty',
                'password' => Hash::make('password123'),
                'email' => 'faculty@upcebu.edu.ph',
                'first_name' => 'Jane',
                'last_name' => 'Professor',
                'role' => 'Faculty',
                'department' => 'Physics',
                'college' => 'College of Arts and Sciences (CAS)',
                'permissions' => json_encode(['Can Book', 'User Type Only'])
            ],
            [
                'username' => 'sysadmin',
                'password' => Hash::make('password123'),
                'email' => 'sysadmin@upcebu.edu.ph',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'role' => 'SYSADMIN',
                'department' => 'N/A - Administration',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'])
            ],
            [
                'username' => 'ao',
                'password' => Hash::make('password123'),
                'email' => 'ao@upcebu.edu.ph',
                'first_name' => 'Administrative',
                'last_name' => 'Officer',
                'role' => 'AO',
                'department' => 'N/A - Administration',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Staff Work'])
            ],
            [
                'username' => 'adpd',
                'password' => Hash::make('password123'),
                'email' => 'adpd@upcebu.edu.ph',
                'first_name' => 'Academic',
                'last_name' => 'Planner',
                'role' => 'ADPD',
                'department' => 'N/A - Administration',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Edit'])
            ],
            [
                'username' => 'ocs',
                'password' => Hash::make('password123'),
                'email' => 'ocs@upcebu.edu.ph',
                'first_name' => 'Office',
                'last_name' => 'Coordinator',
                'role' => 'OCS',
                'department' => 'N/A - Administration',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Edit', 'Can Book'])
            ],
            [
                'username' => 'dptapr',
                'password' => Hash::make('password123'),
                'email' => 'dptapr@upcebu.edu.ph',
                'first_name' => 'Department',
                'last_name' => 'Approver',
                'role' => 'DPTAPR',
                'department' => 'Computer Science',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Approve', 'Can Book', 'User Type Only'])
            ],
            [
                'username' => 'user',
                'password' => Hash::make('password123'),
                'email' => 'user@upcebu.edu.ph',
                'first_name' => 'Regular',
                'last_name' => 'User',
                'role' => 'USER',
                'department' => 'Computer Science',
                'college' => 'College of Engineering (CoE)',
                'permissions' => json_encode(['Can Book', 'User Type Only'])
            ],
        ];

        foreach ($users as $userData) {
            UserAccount::create($userData);
        }
    }
}

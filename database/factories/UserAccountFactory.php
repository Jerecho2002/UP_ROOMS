<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserAccountFactory extends Factory
{
    public function definition(): array
    {
        $roles = ['Admin', 'Staff', 'Faculty', 'DPTAPR', 'AO', 'ADPD', 'OCS', 'SYSADMIN', 'USER'];

        $departments = [
            'Computer Science',
            'Electrical Engineering',
            'Mechanical Engineering',
            'Physics',
            'Mathematics',
            'English/Literature',
            'Accounting',
            'Management',
            'N/A - Administration'
        ];

        $colleges = [
            'College of Engineering (CoE)',
            'College of Arts and Sciences (CAS)',
            'College of Business and Accountancy (CBA)',
            'College of Education (CoEd)',
            'College of Information Technology (CIT)',
            'Graduate School (GS)'
        ];

        $role = $this->faker->randomElement($roles);
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'username' => strtolower($firstName[0] . $lastName),
            'password' => Hash::make('password123'),
            'email' => strtolower($firstName . '.' . $lastName) . '@upcebu.edu.ph',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role,
            'department' => $this->faker->randomElement($departments),
            'college' => $this->faker->randomElement($colleges),
            'permissions' => $this->getPermissionsForRole($role),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    private function getPermissionsForRole(string $role): array
    {
        $permissionsMap = [
            'Admin' => ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
            'Staff' => ['Can Book', 'Staff Work'],
            'Faculty' => ['Can Book', 'User Type Only'],
            'DPTAPR' => ['Can Approve', 'Can Book', 'User Type Only'],
            'AO' => ['Can Approve', 'Can Edit', 'Staff Work'],
            'ADPD' => ['Can Approve', 'Can Edit'],
            'OCS' => ['Can Approve', 'Can Edit', 'Can Book'],
            'SYSADMIN' => ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
            'USER' => ['Can Book', 'User Type Only']
        ];

        return $permissionsMap[$role] ?? ['Can Book', 'User Type Only'];
    }
}

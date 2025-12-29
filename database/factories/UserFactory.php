<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        $roles = ['Admin', 'Staff', 'Faculty', 'DPTAPR', 'AO', 'ADPD', 'OCS', 'SYSADMIN', 'USER'];
        $departments = ['Computer Science', 'Electrical Engineering', 'Mechanical Engineering', 'Physics', 'Mathematics'];
        $colleges = [
            'College of Engineering (CoE)',
            'College of Arts and Sciences (CAS)',
            'College of Business and Accountancy (CBA)',
            'College of Education (CoEd)',
            'College of Information Technology (CIT)'
        ];

        $role = $this->faker->randomElement($roles);

        // Define permissions based on role
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

        return [
            'username' => 'user_' . $this->faker->unique()->numberBetween(1, 1000),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'role' => $role,
            'department' => $this->faker->randomElement($departments),
            'college' => $this->faker->randomElement($colleges),
            'permissions' => $permissionsMap[$role] ?? [],
        ];
    }
}

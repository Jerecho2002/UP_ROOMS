<?php

namespace Database\Factories;

use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'employee_number' => $this->faker->unique()->bothify('EMP-#####'),
            'gender' => $this->faker->optional()->randomElement(['male', 'female', 'other']),
            'contact_number' => $this->faker->optional()->phoneNumber(),
            'college_id' => null,
            'department_id' => null,
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended', 'pending']),
        ];
    }
}

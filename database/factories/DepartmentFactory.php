<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\College;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'department_name' => 'Department of ' . $this->faker->word(),
            'college_id' => College::inRandomOrder()->first()?->id,
            'description' => $this->faker->sentence(8),
        ];
    }
}

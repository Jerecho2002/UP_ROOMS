<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CollegeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'college_name' => 'College of ' . $this->faker->word(),
            'description' => $this->faker->sentence(12),
        ];
    }
}

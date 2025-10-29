<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'building_name' => $this->faker->company() . ' Building',
            'address' => $this->faker->address(),
            'description' => $this->faker->sentence(10),
        ];
    }
}

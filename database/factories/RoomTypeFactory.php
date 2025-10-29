<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_type_name' => $this->faker->randomElement(['Lecture Room', 'Laboratory', 'Office', 'Auditorium']),
            'description' => $this->faker->sentence(8),
        ];
    }
}

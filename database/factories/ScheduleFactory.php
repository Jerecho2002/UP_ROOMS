<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Room;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::inRandomOrder()->first()?->id,
            'course_name' => 'Course ' . strtoupper($this->faker->lexify('???')),
            'day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_time' => $this->faker->time('H:i', '15:00'),
            'end_time' => $this->faker->time('H:i', '20:00'),
            'cfic_id' => strtoupper($this->faker->bothify('CFIC-###')),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TermFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 year', 'now');
        $end = (clone $start)->modify('+4 months');

        return [
            'term_name' => 'Term ' . $this->faker->randomElement(['1st Semester', '2nd Semester', 'Summer']),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ];
    }
}

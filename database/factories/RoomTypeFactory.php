<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory  // NOT DatabaseSeeder
{
    protected $model = RoomType::class;

    public function definition()
    {
        $baseTypes = ['Classroom', 'Laboratory', 'Lecture Hall', 'Conference Room'];

        // Generate unique name with suffix
        $type = $this->faker->randomElement($baseTypes) . ' ' . $this->faker->unique()->numberBetween(1, 100);
        $slug = strtolower(str_replace(' ', '-', $type));

        return [
            'room_type_name' => $type,
            'slug' => $slug,
            'description' => $this->faker->sentence,
            'default_capacity' => $this->faker->numberBetween(20, 200),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'room_name' => 'Room ' . $this->faker->unique()->numberBetween(100, 999),
            'room_code' => 'RM-' . $this->faker->unique()->numberBetween(1000, 9999),
            'building_id' => Building::factory(),
            'college_id' => College::factory(),
            'department_id' => Department::factory(),
            'room_type_id' => RoomType::factory(),
            'assigned_user_id' => null,
            'floor_number' => $this->faker->numberBetween(1, 5),
            'location' => $this->faker->words(3, true),
            'capacity' => $this->faker->numberBetween(20, 100),
            'facilities' => json_encode([
                'tables' => $this->faker->numberBetween(10, 50),
                'chairs' => $this->faker->numberBetween(20, 100),
                'whiteboards' => $this->faker->numberBetween(1, 3),
                'projectors' => $this->faker->numberBetween(0, 2),
            ]),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance', 'closed']),
            'notes' => $this->faker->sentence,
        ];
    }
}

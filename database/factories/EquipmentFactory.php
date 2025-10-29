<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomType;
use App\Models\Building;
use App\Models\College;

class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'equipment_name' => ucfirst($this->faker->word()) . ' Equipment',
            'description' => $this->faker->sentence(10),
            'quantity' => $this->faker->numberBetween(1, 20),
            'room_type_id' => RoomType::inRandomOrder()->first()?->id,
            'building_id' => Building::inRandomOrder()->first()?->id,
            'college_id' => College::inRandomOrder()->first()?->id,
        ];
    }
}

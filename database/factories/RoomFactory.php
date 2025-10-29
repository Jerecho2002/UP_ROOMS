<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Building;
use App\Models\College;
use App\Models\RoomType;
use App\Models\UserAccount;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_name' => 'Room ' . $this->faker->unique()->numberBetween(100, 499),
            'building_id' => Building::inRandomOrder()->first()?->id,
            'college_id' => College::inRandomOrder()->first()?->id,
            'capacity' => $this->faker->numberBetween(20, 100),
            'location' => $this->faker->city(),
            'room_type_id' => RoomType::inRandomOrder()->first()?->id,
            'user_account_id' => UserAccount::inRandomOrder()->first()?->id,
        ];
    }
}

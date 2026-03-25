<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition()
    {
        $equipmentTypes = [
            'Laptop',
            'Desktop Computer',
            'Projector',
            'Printer',
            'Scanner',
            'Microphone',
            'Speaker System',
            'Whiteboard',
            'Television',
            'Camera',
            'Tablet',
            'Server',
            'Router',
            'Switch'
        ];

        $equipmentName = $this->faker->randomElement($equipmentTypes);

        return [
            'equipment_name' => $equipmentName,
            'inventory_id' => 'INV-' . $this->faker->unique()->numberBetween(10000, 99999),
            'property_id' => 'PROP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence,
            'quantity' => $this->faker->numberBetween(1, 10),
            'cfic_id' => 'CFIC-' . $this->faker->unique()->bothify('??##'),
            'serial_number' => $this->faker->unique()->bothify('SN-########'),
        ];
    }
}

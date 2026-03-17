<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            \App\Models\UserAccount::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}

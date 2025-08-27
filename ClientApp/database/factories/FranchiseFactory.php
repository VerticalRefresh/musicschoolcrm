<?php

namespace Database\Factories;
use App\Models\{Franchise, Employee};

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Franchise>
 */
class FranchiseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => Employee::factory(),
            'phone' => fake()->e164PhoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'timezone' => 'America/New York',
        ];
    }
}

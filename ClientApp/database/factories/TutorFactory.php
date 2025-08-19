<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Validation\Rules\Unique;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tutor>
 */
class TutorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name'            => fake()->firstName(),
            'last_name'             => fake()->lastName(),
            'email'                 => fake()->unique()->safeEmail(),
            'phone'                 => fake()->e164PhoneNumber(),
            'balance'               => fake()->randomFloat(2, 0, 200),
            'certification'         => fake()->numberBetween(1, 5),
            'age_group'             => fake()->randomElement(['kids', 'teens', 'adults', 'all']),
        ];
    }
}

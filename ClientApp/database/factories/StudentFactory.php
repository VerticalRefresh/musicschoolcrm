<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
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
            'subscription'          => fake()->randomFloat(2, 40, 200),
            'balance'               => fake()->randomFloat(2, 0, 150),
            'birthday'              => fake()->dateTimeBetween('-18 years', '-6 years')->format('Y-m-d'),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'line1'             => fake()->streetAddress(),
            'line2'             => null,
            'city'              => fake()->city(),
            'region'            => 'NY',
            'postal_code'       => fake()->postcode(),
            'country_code'      => 'US',
        ];
    }
}

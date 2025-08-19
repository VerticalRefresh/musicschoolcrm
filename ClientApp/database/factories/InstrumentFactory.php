<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instrument>
 */
class InstrumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pairs = [
            ['Guitar', 'Strings'], ['Violin', 'Strings'], ['Cello', 'Strings'], 
            ['Piano', 'Keys'], ['Flute', 'Woodwind'], ['Trumpet', 'Brass'],
            ['Drums', 'Percussion'], ['Saxophone', 'Woodwind'],
        ];

        [$name,$category] = fake()->randomElement($pairs);
        return [
            'name'      => $name,
            'category'  => $category,
        ];
    }
}

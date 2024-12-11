<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_length' => fake()->numberBetween(9, 13),
            'phone_code' => fake()->countryCode(),
            'name' => fake()->country(),
            'abbr' => fake()->countryISOAlpha3(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Contracts\Enums\TransactionTypes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->uuid(),
            'user_id' => User::factory(),
            'name' => fake()->randomElement(TransactionTypes::values()),
            'currency' => fake()->currencyCode(),
            'channel' => fake()->randomElement(['paystack', 'flutterwave', 'korrapay']),
            'icon' => fake()->imageUrl(),
            'amount' => fake()->numberBetween(10, 5000),
        ];
    }
}

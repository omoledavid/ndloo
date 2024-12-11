<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender' => User::factory(),
            'recipient' => User::factory(),
            'content' => fake()->sentences(asText: true),
            'media' => [fake()->imageUrl()],
            'read' => fake()->boolean(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\GiftPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGift>
 */
class UserGiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'sender_id' => User::inRandomOrder()->first()->id,
            'gift_plan_id' => GiftPlan::inRandomOrder()->first()->id,
        ];
    }
}

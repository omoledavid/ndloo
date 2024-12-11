<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->userName(),
            'phone' => fake()->phoneNumber(),
            'age' => fake()->numberBetween(18, 60),
            'gender' => fake()->randomElement(['male', 'female']),
            'dob' => fake()->date('Y-m-d H:i:s'),
            'avatar' => fake()->imageUrl(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'wallet' => fake()->numberBetween(0, 5000),
            'credits' => fake()->numberBetween(0, 5000),
            'country_id' => Country::query()->inRandomOrder()->first()->id,
            'active' => fake()->boolean(),
            'status' => fake()->numberBetween(0, 1),
            'is_admin' => fake()->boolean(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

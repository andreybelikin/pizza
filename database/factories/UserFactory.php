<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'surname' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'is_admin' => false,
            'password' => bcrypt(fake()->password()),
            'default_address' => fake()->unique()->address(),
            'address' => fake()->unique()->address(),
        ];
    }

    public function setPassword(string $password): Factory
    {
        return $this->state([
            'password' => bcrypt($password),
        ]);
    }
}

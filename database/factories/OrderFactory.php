<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::all();

        return [
            'status' => $this->faker->randomElement(OrderStatus::getStatuses()),
            'user_id' => $users->random()->id,
            'address' => $this->faker->address(),
            'total' => $this->faker->numberBetween(1000, 999999),
            'phone' => $this->faker->phoneNumber(),
            'name' => $this->faker->name(),
        ];
    }
}

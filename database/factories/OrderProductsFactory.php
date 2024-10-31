<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProducts>
 */
class OrderProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::all()->random();

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(ProductType::getTypes()),
            'price' => $this->faker->numberBetween(1000, 999999),
            'order_id' => $order->id,
        ];
    }
}

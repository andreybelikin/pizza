<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
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

        $quantity = $this->faker->numberBetween(1, 10);
        $price = $this->faker->numberBetween(1000, 999999);
        $total = $price * $quantity;

        $order->total = $total;
        $order->save();

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(ProductType::getTypes()),
            'quantity' => $quantity,
            'price' => $price,
            'order_id' => $order->id,
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::factory(10)->create();

        foreach ($orders as $order) {
            $products = Product::query()
                ->limit(3)
                ->inRandomOrder()
                ->get([
                    'title',
                    'description',
                    'type',
                    'price',
                ]);

            foreach ($products as $product) {
                $quantity = rand(1, 5);
                $order->orderProducts()->create([
                    'title' => $product->title,
                    'description' => $product->description,
                    'quantity' => $quantity,
                    'type' => $product->type,
                    'price' => $product->price,
                ]);
                $order->total += $quantity * $product->price;
                $order->save();
            }
        }
    }
}

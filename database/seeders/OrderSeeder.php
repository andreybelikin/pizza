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
        $products = Product::query()
            ->limit(10)
            ->get([
                'title',
                'description',
                'type',
                'price',
            ]);
        $orders = Order::factory(10)->create();

        foreach ($orders as $key => $order) {
            $product = $products[$key];
            $order->orderProducts()->create([
                'title' => $product->title,
                'description' => $product->description,
                'type' => $product->type,
                'price' => $product->price,
            ]);
        }
    }
}

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
        $products = Product::all();
        $orders = Order::factory(10)->create();

        foreach ($orders as $order) {
            $order->products()->attach($products->random(random_int(1, 3)));
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->inRandomOrder()
            ->get()
        ;

        $products = Product::factory(15)->create();
        foreach ($products as $product) {
            $product
                ->users()
                ->attach($users->random(2))
            ;
        }
    }
}

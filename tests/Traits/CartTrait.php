<?php

namespace Tests\Traits;

use App\Enums\Limit\Cart\CartProductLimit;
use App\Models\Product;
use App\Models\User;

trait CartTrait
{
    public function getCart(User $user): array
    {
        $products = $user->products()
            ->distinct()
            ->get()
            ->toArray();

        $cart['products'] = array_map(function (array $product) use ($user) {
            $productQuantity = $user
                ->products()
                ->where('products.id', $product['id'])
                ->count();

            return [
                'id' => $product['id'],
                'quantity' => $productQuantity,
                'title' => $product['title'],
                'price' => floor($product['price']),
                'totalPrice' => $product['price'] * $productQuantity,
            ];
        }, $products);
        $cart['totalSum'] = array_sum(array_column($cart['products'], 'totalPrice'));

        return $cart;
    }

    public function createCartProducts(User $user): void
    {
        $quantityCount = 3;
        $limitedProducts = array_map(function (string $limit) use ($quantityCount)  {
            return Product::query()
                ->take($quantityCount)
                ->where('type', $limit)
                ->get();
        }, CartProductLimit::getTypes());

        for ($i = 0; $i < $quantityCount; $i++) {
            $user->products()->attach(array_column($limitedProducts, 'id'));
        }
    }
}

<?php

namespace Tests\Traits;

use App\Enums\Limit\CartProductLimit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

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

    public function createUserCartProducts(User $user, array $products): void
    {
        $user->products()->delete();
        $user->products()->createMany($products);
    }

    public function createCartProducts(User $user): void
    {
        $quantityCount = 3;
        $limitedProducts = [];

        foreach (CartProductLimit::getLimits() as $limit) {
            $products = Product::query()
                ->take($quantityCount)
                ->where('type', $limit)
                ->get();
            $limitedProducts = [...$limitedProducts, ...$products];
        }

        for ($i = 0; $i < $quantityCount; $i++) {
            $user->products()->attach(array_column($limitedProducts, 'id'));
        }
    }
}

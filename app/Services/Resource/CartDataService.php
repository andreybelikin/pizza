<?php

namespace App\Services\Resource;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class CartDataService
{
    private User $cartUser;

    public function setCartUser(int $userId): self
    {
        $this->cartUser = User::query()->find($userId);

        return $this;
    }

    public function deleteCart(): void
    {
        $this->cartUser
            ->products()
            ->detach();
    }

    public function isCartExists(): bool
    {
        return $this->cartUser
            ->products()
            ->exists();
    }

    public function getCartProducts(): array
    {
        $cartProducts = $this->cartUser
            ->products()
            ->distinct()
            ->get()
            ->toArray();

        $totalSum = 0;
        array_walk($cartProducts, function ($cartProduct) use (&$totalSum) {
                $productQuantity = $this->getCartProductQuantity($cartProduct['id']);

                $cartProduct['quantity'] = $productQuantity;
                $cartProduct['totalPrice'] = $cartProduct['price'] * $productQuantity;
                $totalSum += $cartProduct['totalPrice'];
        });

        return [
            'products' => $cartProducts,
            'totalSum' => $totalSum,
        ];
    }

    public function getCartProductsById(array $productsIds): EloquentCollection
    {
        return $this->cartUser
            ->products()
            ->whereIn('products.id', $productsIds)
            ->get();
    }

    public function addCartProducts(int $productId, int $quantity): void
    {
        $preparedProducts = array_fill(0, $quantity, $productId);
        $this->cartUser
            ->products()
            ->attach($preparedProducts);
    }

    public function deleteCartProduct(int $productId, int $limit = 0): void
    {
        DB::table('cart_product')
            ->where('product_id', $productId)
            ->where('user_id', $this->cartUser->id)
            ->when($limit > 0, function ($query) use ($limit) {
                return $query->limit($limit);
            })
            ->delete();
    }

    public function getCartProductQuantity(int $productId): int
    {
        return $this->cartUser
            ->products()
            ->where('products.id', $productId)
            ->count();
    }
}

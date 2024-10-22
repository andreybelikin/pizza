<?php

namespace App\Services\Resource;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class CartDataService
{
    private User $cartUser;

    public function setCartUser(int $userId): self
    {
        $this->cartUser = User::find($userId);

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

    public function getCartProducts(): EloquentCollection
    {
        return $this->cartUser
            ->products()
            ->distinct()
            ->get();
    }

    public function addProductsToCart(int $productId, int $quantity): void
    {
        $preparedProducts = array_fill(0, $quantity, $productId);
        $this->cartUser
            ->products()
            ->attach($preparedProducts);
    }

    public function deleteProductFromCart(int $productId, int $limit): void
    {
        $this->cartUser
            ->products()
            ->limit($limit)
            ->where('products.id', $productId)
            ->detach();
    }

    public function getProductQuantity(int $productId): int
    {
        return $this->cartUser
            ->products()
            ->where('products.id', $productId)
            ->count();
    }
}

<?php

namespace App\Services\Resource;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

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

    public function deleteCartProduct(int $productId, int $limit): void
    {
        DB::table('cart_product')
            ->where('product_id', $productId)
            ->where('user_id', $this->cartUser->id)
            ->limit($limit)
            ->delete();
//        dd($this->cartUser->products()->get());
    }

    public function getProductQuantity(int $productId): int
    {
        return $this->cartUser
            ->products()
            ->where('products.id', $productId)
            ->count();
    }
}

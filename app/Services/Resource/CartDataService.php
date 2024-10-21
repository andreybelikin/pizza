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

    public function getCart(): SupportCollection
    {
        $cartProducts = $this->getCartProducts();

        return $cartProducts->map(function (Product $cartProduct) {
            return [
                'id' => $cartProduct['id'],
                'title' => $cartProduct['title'],
                'quantity' => $this->getProductQuantity($cartProduct['id']),
                'price' => $cartProduct['price'],
            ];
        });
    }

    public function deleteCart(): void
    {
        $this->cartUser
            ->products()
            ->detach();
    }

    public function cartExists(): bool
    {
        return $this->cartUser
            ->products()
            ->exists();
    }

    private function getCartProducts(): EloquentCollection
    {
        return $this->cartUser
            ->products()
            ->distinct()
            ->get();
    }

    private function addProductsToCart(int $productId, int $quantity): void
    {
        $preparedProducts = array_fill(0, $quantity, $productId);
        $this->cartUser
            ->products()
            ->attach($preparedProducts);
    }

    public function updateCartByRequestProducts(array $requestProducts): void
    {
        $cartProducts = $this->getCartProducts();

        foreach ($requestProducts as $requestProduct) {
            $requestProductId = $requestProduct['id'];
            $requestProductQuantity = $requestProduct['quantity'];

            $matchedCartProduct = $cartProducts->first(
                fn (Product $cartProduct) => $cartProduct->id === $requestProductId
            );

            if (is_null($matchedCartProduct) && $requestProductQuantity > 0) {
                $this->addProductsToCart($requestProductId, $requestProductQuantity);
                continue;
            }

            $matchedCartProductQuantity = $this->getProductQuantity($matchedCartProduct->id);
            $quantityDiff = $requestProductQuantity - $matchedCartProductQuantity;

            switch ($quantityDiff) {
                case 0:
                    $this->deleteProductInCart($requestProductId, 0);
                    break;
                case $quantityDiff > 0:
                    $this->addProductsToCart($requestProductId, $quantityDiff);
                    break;
                case $quantityDiff < 0:
                    $this->deleteProductInCart($requestProductId, abs($quantityDiff));
                    break;
            }
        }
    }

    private function deleteProductInCart(int $productId, int $limit): void
    {
        $this->cartUser
            ->products()
            ->limit($limit)
            ->where('products.id', $productId)
            ->detach();
    }

    private function getProductQuantity(int $productId): int
    {
        return $this->cartUser
            ->products()
            ->where('products.id', $productId)
            ->count();
    }
}

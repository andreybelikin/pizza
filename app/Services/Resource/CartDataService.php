<?php

namespace App\Services\Resource;

use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class CartDataService
{
    private const CARTS_PER_PAGE = 15;
    private User $cartUser;
    private array $cartUsers;

    public function setCartUser(int $userId): self
    {
        $this->cartUser = User::query()->find($userId);

        return $this;
    }

    public function setCartUsers(array $cartUsers): self
    {
        $this->cartUsers = $cartUsers;

        return $this;
    }

    public function getCartProducts(): Collection
    {
        $cartWithQuantity = $this->getCartWithQuantity();

        return $cartWithQuantity->map(function ($cartProduct) {
            $product = Product::query()->find($cartProduct->id);

            return [
                'id' => $product->id,
                'title' => $product->title,
                'quantity' => $cartProduct->quantity,
                'price' => $product->price,
            ];
        });
    }

    public function deleteCart(): void
    {
        $this->cartUser
            ->cart
            ->delete();
    }

    public function cartExists(): bool
    {
        return $this->cartUser
            ->cart
            ->exists();
    }

    public function getCarts(): LengthAwarePaginator
    {
        return User::query()
            ->find($this->cartUsers)
            ->cart
            ->paginate(self::CARTS_PER_PAGE);
    }

    private function getCartWithQuantity(): Collection
    {
        return $this->cartUser
            ->cart
            ->select(['product_id', DB::raw('COUNT(*) as quantity')])
            ->groupBy(['product_id'])
            ->get();
    }

    private function addProductsToCart(int $productId, int $quantity): void
    {
        $preparedProducts = array_fill(0, $quantity, $productId);
        $this->cartUser
            ->cart
            ->attach($preparedProducts);
    }

    public function updateCartByRequestProducts(array $requestProducts): void
    {
        $cartProducts = $this->getCartWithQuantity();

        foreach ($requestProducts as $requestProduct) {
            $requestProductId = $requestProduct['id'];
            $requestProductQuantity = $requestProduct['quantity'];

            $cartProductMatch = $cartProducts->first(
                fn (CartProduct $cartProduct) => $cartProduct->id === $requestProductId
            );

            if (is_null($cartProductMatch) && $requestProductQuantity > 0) {
                $this->addProductsToCart($requestProductId, $requestProductQuantity);
                continue;
            }

            $quantityDiff = $requestProductQuantity - $cartProductMatch->quantity;

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
            ->cart
            ->limit($limit)
            ->find($productId)
            ->delete();
    }
}

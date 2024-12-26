<?php

namespace App\Services\Resource;

use App\Dto\CartData;
use App\Dto\CartProductData;
use App\Models\CartProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartDataService
{
    public function deleteCart(int $userId): void
    {
        CartProduct::query()
            ->where('user_id', $userId)
            ->delete();
    }

    public function getCart(int $userId): CartData
    {
        $cartProducts = CartProduct::query()
            ->select('product_id', DB::raw('COUNT(*) as quantity'))
            ->with('product')
            ->where('user_id', $userId)
            ->groupBy('product_id')
            ->get()
            ->toBase();
        $cartProductsData = $cartProducts->map(function (CartProduct $cartProduct) use ($userId, $cartProducts) {
            return new CartProductData(
                id: $cartProduct->product->id,
                title: $cartProduct->product->title,
                description: $cartProduct->product->description,
                type: $cartProduct->product->type,
                price: $cartProduct->product->price,
                quantity: $cartProduct->quantity,
                totalPrice: $cartProduct->quantity * $cartProduct->product->price
            );
        });

        if ($cartProducts->isEmpty()) {
            throw new NotFoundHttpException();
        }

        return new CartData(
            products: $cartProductsData,
            totalSum: $cartProductsData->sum('totalPrice')
        );
    }

    public function getCartProductsById(array $productsIds, int $userId): EloquentCollection
    {
        return CartProduct::query()
            ->where('user_id', $userId)
            ->whereIn('product_id', $productsIds)
            ->get();
    }

    public function addCartProducts(int $productId, int $userId, int $quantity): void
    {
        $productsArray = array_fill(0, $quantity, $productId);

        foreach ($productsArray as $productId) {
            $cartProduct = new CartProduct([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $cartProduct->save();
        }
    }

    public function deleteCartProduct(int $productId, int $userId, int $limit = 0): void
    {
        CartProduct::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->when($limit > 0, function ($query) use ($limit) {
                return $query->limit($limit);
            })
            ->delete();
    }

    private function getUserCartProducts(string $userId): BelongsToMany
    {
        return User::query()
            ->findOrFail($userId)
            ->refresh()
            ->products();
    }
}

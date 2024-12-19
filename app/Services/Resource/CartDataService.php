<?php

namespace App\Services\Resource;

use App\Dto\CartData;
use App\Dto\CartProductData;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartDataService
{
    private ?BelongsToMany $userCartProducts;

    public function deleteCart(string $userId): void
    {
        $this->getUserCartProducts($userId)->detach();
    }

    public function getCart(string $userId): CartData
    {
        $cartProductsEntries = $this->getUserCartProducts($userId)
            ->select(
                'products.id',
                'products.title',
                'products.description',
                'products.type',
                'products.price',
                'user_id',
                \DB::raw('COUNT(products.id) as quantity'),
                \DB::raw('(products.price * COUNT(products.id)) as totalPrice')
            )
            ->groupBy(
                'products.id',
                'products.title',
                'products.description',
                'products.type',
                'products.price',
                'user_id',
            )
            ->get()
            ->toBase();

        if ($cartProductsEntries->isEmpty()) {
            throw new NotFoundHttpException();
        }

        $cartProductsData = CartProductData::createFromDB($cartProductsEntries);

        return new CartData(
            products: $cartProductsData,
            totalSum: $cartProductsEntries->sum('totalPrice')
        );
    }

    public function getCartProductsById(array $productsIds, string $userId): EloquentCollection
    {
        return $this->getUserCartProducts($userId)
            ->whereIn('products.id', $productsIds)
            ->get();
    }

    public function addCartProducts(int $productId, string $userId, int $quantity): void
    {
        $preparedProducts = array_fill(0, $quantity, $productId);
        $this->getUserCartProducts($userId)
            ->attach($preparedProducts);
    }

    public function deleteCartProduct(int $productId, string $userId, int $limit = 0): void
    {
        $this->getUserCartProducts($userId)
            ->where('product_id', $productId)
            ->when($limit > 0, function ($query) use ($limit) {
                return $query->limit($limit);
            })
            ->delete();
    }

    private function getUserCartProducts(string $userId): BelongsToMany
    {
        $this->userCartProducts = $this->userCartProducts ?? User::query()
            ->findOrFail($userId)
            ->products();

        return $this->userCartProducts;
    }
}

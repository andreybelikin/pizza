<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartAddRequest;
use App\Http\Requests\Cart\CartProductsDeleteRequest;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Models\User;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResourceService
{
    public function __construct(private CartLimitService $cartLimitService)
    {}

    public function addCart(CartAddRequest $request, string $userId): void
    {
        $this->checkActionPermission('update', $userId);
        $products = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($products);
        $this->addProductsToCart($products);
    }

    public function getCart(string $userId): JsonResource
    {
        $this->checkActionPermission('get', $userId);
        $cart = $this->getCartFromDB($userId);

        return new CartResource($cart);
    }

    public function updateCart(CartUpdateRequest $request, string $userId): JsonResource
    {
        $this->ensureCartFound($userId);
        $this->checkActionPermission('update', $userId);

        $products = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($products);

        $product = $request['products'][0];

        if ($product['quantity'] > 0) {
            $this->addProductsToCart($products);
        }

        if ($product['quantity'] === 0) {
            $this->deleteProducts([$product['id']], $userId);
        }

        return $this->getCart($userId);
    }

    public function deleteCart(string $userId): void
    {
        $this->ensureCartFound($userId);
        $this->checkActionPermission('delete', $userId);
        CartProduct::emptyCart($userId);
    }

    public function deleteCartProducts(CartProductsDeleteRequest $request, string $userId): void
    {
        $this->checkActionPermission('delete', $userId);
        $this->deleteProducts($request->input('products'), $userId);
    }

    private function addProductsToCart(array $products): void
    {
        $preparedProducts = $this->prepareProductsToAdd($products);
        CartProduct::addProductsToCart($preparedProducts);
    }

    private function prepareProductsToAdd(array $products): array
    {
        $preparedProducts = [];

        foreach ($products as $product) {
            for ($i = 0; $i < $product['quantity']; $i++) {
                $preparedProducts[] = [
                    'product_id' => $product['id'],
                    'user_id' => auth()->user()->getAuthIdentifier(),
                ];
            }
        }

        return $preparedProducts;
    }

    private function deleteProducts(array $productsIds, string $userId): void
    {
        CartProduct::deleteCartProducts($productsIds, $userId);
    }

    private function getCartFromDB(string $userId): array
    {
        return CartProduct::getCart($userId);
    }

    private function getCartUser(string $userId): User
    {
        return User::query()->find((int) $userId);
    }

    private function checkActionPermission(string $resourceAction, string $cartUserId): void
    {
        $authorizedUser = auth()->user();
        $cartUser = $this->getCartUser($cartUserId);

        if ($authorizedUser->cant($resourceAction, $cartUser)) {
            throw new ResourceAccessException();
        }
    }

    private function ensureCartFound(string $userId): void
    {
        $cart = $this->getCartFromDB($userId);

        if (empty($cart)) {
            throw new ResourceNotFoundException();
        }
    }
}

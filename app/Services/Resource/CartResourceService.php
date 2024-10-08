<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartAddRequest;
use App\Http\Requests\Cart\CartProductsDeleteRequest;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResourceService
{
    private int $cartUserId;

    public function __construct(private CartLimitService $cartLimitService)
    {}

    public function setCartUser(string $userId): self
    {
        $this->cartUserId = (int) $userId;

        return $this;
    }

    public function addCart(CartAddRequest $request): void
    {
        $this->ensureUserIsCartOwner();
        $products = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($products);
        $this->addProductsToCart($products);
    }

    public function getCart(): JsonResource
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();

        return $this->buildCartResource();
    }

    public function updateCart(CartUpdateRequest $request): JsonResource
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();

        $products = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($products);

        $product = $request['products'][0];

        if ($product['quantity'] > 0) {
            $this->addProductsToCart($products);
        }

        if ($product['quantity'] === 0) {
            $this->deleteProducts([$product['id']]);
        }

        return $this->buildCartResource();
    }

    public function deleteCart(): void
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();
        CartProduct::emptyCart($this->cartUserId);
    }

    public function deleteCartProducts(CartProductsDeleteRequest $request): void
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();
        $this->deleteProducts($request->input('products'));
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
                    'user_id' => $this->cartUserId,
                ];
            }
        }

        return $preparedProducts;
    }

    private function deleteProducts(array $productsIds): void
    {
        CartProduct::deleteCartProducts($productsIds, $this->cartUserId);
    }

    private function buildCartResource(): JsonResource
    {
        $cartProducts = CartProduct::getCartDistinctProducts($this->cartUserId);
        $cartProductsIds = array_column($cartProducts, 'id');

        $products = Product::whereIn('id', $cartProductsIds)
            ->get()
            ->toArray()
        ;

        $cart = array_map(function($cartProduct, $product) {
            return [
                'id' => $product['id'],
                'quantity' => $cartProduct['quantity'],
                'title' => $product['title'],
                'price' => $product['price'],
            ];
        }, $cartProducts, $products);

        return new CartResource($cart);
    }

    private function ensureCartExists(): void
    {
        if (!CartProduct::where('user_id', '=', $this->cartUserId)->exists()) {
            throw new ResourceNotFoundException();
        }
    }

    private function ensureUserIsCartOwner(): void
    {
        $authorizedUserId = auth()->user()->getAuthIdentifier();

        if ($this->cartUserId !== $authorizedUserId) {
            throw new ResourceAccessException();
        }
    }
}

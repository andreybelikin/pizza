<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResourceService
{
    private int $cartUserId;

    public function __construct(
        private CartLimitService $cartLimitService,
        private CartDataService $cartDataService
    ) {}

    public function setCartUser(string $userId): self
    {
        $this->cartUserId = (int) $userId;
        $this->cartDataService->setCartUser($this->cartUserId);

        return $this;
    }

    public function getCart(): JsonResource
    {
        $this->ensureCartExists();
        $this->checkUserPermission();

        return $this->getCartResource();
    }

    public function updateCart(CartUpdateRequest $request): JsonResource
    {
        $this->checkUserPermission();

        $requestProducts = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($requestProducts);
        $this->updateCartByRequestProducts($requestProducts);

        return $this->getCartResource();
    }

    public function deleteCart(): void
    {
        $this->ensureCartExists();
        $this->checkUserPermission();
        $this->cartDataService->deleteCart();
    }

    private function getCartResource(): JsonResource
    {
        $cart = $this->cartDataService->getCartProducts();

        $cartResource['products'] = $cart->map(function (Product $cartProduct) {
            $cartProductQuantity = $this->cartDataService->getProductQuantity($cartProduct['id']);

            return [
                'id' => $cartProduct['id'],
                'quantity' => $cartProductQuantity,
                'title' => $cartProduct['title'],
                'price' => $cartProduct['price'],
                'totalPrice' => $cartProduct['price'] * $cartProductQuantity,
            ];
        });
        $cartResource['totalSum'] = $cartResource['products']->sum('totalPrice');

        return new CartResource($cartResource);
    }

    private function ensureCartExists(): void
    {
        if (!$this->cartDataService->isCartExists()) {
            throw new ResourceNotFoundException();
        }
    }

    private function updateCartByRequestProducts(array $requestProducts): void
    {
        $cartProducts = $this->cartDataService->getCartProducts();

        foreach ($requestProducts as $requestProduct) {
            $requestProductId = $requestProduct['id'];
            $requestProductQuantity = $requestProduct['quantity'];

            if ($requestProductQuantity === 0) {
                $this->cartDataService->deleteProductFromCart($requestProductId, 0);
                continue;
            }

            $matchedCartProduct = $cartProducts->find($requestProductId);

            if (is_null($matchedCartProduct)) {
                $this->cartDataService->addProductsToCart($requestProductId, $requestProductQuantity);
                continue;
            }

            $matchedCartProductQuantity = $this->cartDataService->getProductQuantity($matchedCartProduct->id);
            $quantityDiff = $requestProductQuantity - $matchedCartProductQuantity;

            switch ($quantityDiff) {
                case $quantityDiff > 0:
                    $this->cartDataService->addProductsToCart($requestProductId, $quantityDiff);
                    break;
                case $quantityDiff < 0:
                    $this->cartDataService->deleteProductFromCart($requestProductId, abs($quantityDiff));
                    break;
            }
        }
    }

    private function checkUserPermission(): void
    {
        $authorizedUser = auth()->user();

        if ($authorizedUser->isAdmin()) {
            return;
        }

        if ($this->cartUserId !== $authorizedUser->getAuthIdentifier()) {
            throw new ResourceAccessException();
        }
    }
}

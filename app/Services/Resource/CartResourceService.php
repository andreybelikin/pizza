<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Services\Limit\CartLimitService;
use Illuminate\Database\Eloquent\Collection;
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
        $this->cartDataService
            ->setCartUser($this->cartUserId)
            ->updateCartByRequestProducts($requestProducts);

        return $this->getCartResource();
    }

    public function deleteCart(): void
    {
        $this->ensureCartExists();
        $this->checkUserPermission();
        $this->cartDataService
            ->setCartUser($this->cartUserId)
            ->deleteCart();
    }

    private function getCartResource(): JsonResource
    {
        $cart = $this->cartDataService
            ->setCartUser($this->cartUserId)
            ->getCart();

        $cartResource['products'] = $cart->map(function (array $cartProduct) {
            return [
                'id' => $cartProduct['id'],
                'quantity' => $cartProduct['quantity'],
                'title' => $cartProduct['title'],
                'price' => $cartProduct['price'],
                'totalPrice' => $cartProduct['price'] * $cartProduct['quantity'],
            ];
        });
        $cartResource['totalSum'] = $cartResource['products']->sum('totalPrice');

        return new CartResource($cartResource);
    }

    private function ensureCartExists(): void
    {
        $cartExistence = $this->cartDataService
            ->setCartUser($this->cartUserId)
            ->cartExists();

        if (!$cartExistence) {
            throw new ResourceNotFoundException();
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

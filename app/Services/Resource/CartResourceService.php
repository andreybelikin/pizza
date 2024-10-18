<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\Limit\CartLimitService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

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

    public function getCarts(array $usersIds): ResourceCollection
    {
        return $this->getCartsResourceCollection($usersIds);
    }

    public function getCarts(): JsonResource
    {
        $this->checkUserPermission();
        $carts = $this->cartDataService->getCarts();

    }

    public function updateCart(CartUpdateRequest $request): JsonResource
    {
        $this->checkUserPermission();

        $requestProducts = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($requestProducts);
        $this->cartDataService->updateCartByRequestProducts($requestProducts);

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
        $cartProducts = $this->cartDataService
            ->setCartUser($this->cartUserId)
            ->getCartProducts();

        $cart['products'] = $cartProducts->map(function (CartProduct $cartProduct) {
            return [
                'id' => $cartProduct->id,
                'quantity' => $cartProduct->quantity,
                'title' => $cartProduct->title,
                'price' => $cartProduct->price,
                'totalPrice' => $cartProduct->price * $cartProduct->quantity,
            ];
        });
        $cart['totalSum'] = $cart['products']->sum('totalPrice');

        return new CartResource($cart);
    }

    private function getCartsResourceCollection(array $usersIds): ResourceCollection
    {
        $paginatedCarts = $this->cartDataService->setCartUsers($usersIds);
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

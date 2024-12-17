<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Models\User;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CartResourceService
{
    public function __construct(
        private CartLimitService $cartLimitService,
        private CartDataService $cartDataService
    ) {}

    public function getCart(string $userId): JsonResource
    {
        Gate::authorize('get', [CartProduct::class, $userId]);
        return $this->getCartResource($userId);
    }

    public function updateCart(CartUpdateRequest $request, string $userId): JsonResource
    {
        Gate::authorize('update', [CartProduct::class, $userId]);
        $requestProducts = $request->input('products');

        $this->cartLimitService->checkQuantityPerTypeLimit($requestProducts);
        $this->updateCartByRequestProducts($requestProducts, $userId);

        return $this->getCartResource($userId);
    }

    public function deleteCart(string $userId): void
    {
        Gate::authorize('delete', [CartProduct::class, $userId]);
        $this->cartDataService->deleteCart($userId);
    }

    private function getCartResource(string $userId): JsonResource
    {
        return new CartResource($this->cartDataService->getCart($userId));
    }

    private function updateCartByRequestProducts(array $requestProducts, string $userId): void
    {
        $requestProductsIds = array_column($requestProducts, 'id');
        $cartProducts = $this->cartDataService->getCartProductsById($requestProductsIds, $userId);

        foreach ($requestProducts as $requestProduct) {
            $requestProductId = $requestProduct['id'];
            $requestQuantity = $requestProduct['quantity'];

            $currentQuantity = $cartProducts
                ->where('id', $requestProductId)
                ->count();

            if ($requestQuantity === 0 && $currentQuantity > 0) {
                $this->cartDataService->deleteCartProduct($requestProductId, $userId);
            } elseif ($requestQuantity > $currentQuantity) {
                $this->cartDataService->addCartProducts(
                    $requestProductId,
                    $userId,
                    $requestQuantity - $currentQuantity
                );
            } elseif ($requestQuantity > 0 && $requestQuantity < $currentQuantity) {
                $this->cartDataService->deleteCartProduct(
                    $requestProductId,
                    $userId,
                    $currentQuantity - $requestQuantity
                );
            }
        }
    }
}

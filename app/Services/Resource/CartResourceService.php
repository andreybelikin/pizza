<?php

namespace App\Services\Resource;

use App\Dto\Request\UpdateCartData;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Services\DBTransactionService;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CartResourceService
{
    public function __construct(
        private CartLimitService $cartLimitService,
        private CartDataService $cartDataService,
        private DBTransactionService $dbTransactionService
    ) {}

    public function getCart(string $userId): JsonResource
    {
        Gate::authorize('get', [CartProduct::class, $userId]);

        return $this->getCartResource($userId);
    }

    public function updateCart(CartUpdateRequest $request, string $userId): JsonResource
    {
        Gate::authorize('update', [CartProduct::class, $userId]);
        $cartUpdateData = UpdateCartData::fromRequest($request);
        $this->cartLimitService->checkQuantityPerTypeLimit($cartUpdateData->products);

        $this->dbTransactionService->execute(function () use ($cartUpdateData) {
            $this->updateCartByRequestProducts($cartUpdateData);
        });

        return $this->getCartResource($userId);
    }

    public function deleteCart(string $userId): void
    {
        Gate::authorize('delete', [CartProduct::class, $userId]);
        $this->dbTransactionService->execute(function () use ($userId) {
            $this->cartDataService->deleteCart($userId);
        });
    }

    private function getCartResource(string $userId): JsonResource
    {
        return new CartResource($this->cartDataService->getCart($userId));
    }

    private function updateCartByRequestProducts(UpdateCartData $updateCartData): void
    {
        Gate::authorize('update', [CartProduct::class, $updateCartData->userId]);
        $requestProductsIds = $updateCartData
            ->products
            ->pluck('id')
            ->toArray();
        $cartProducts = $this->cartDataService->getCartProductsById($requestProductsIds, $updateCartData->userId);

        foreach ($updateCartData->products as $requestProduct) {
            $requestProductId = $requestProduct->id;
            $requestQuantity = $requestProduct->quantity;

            $currentQuantity = $cartProducts
                ->where('id', $requestProductId)
                ->count();

            if ($requestQuantity === 0 && $currentQuantity > 0) {
                $this->cartDataService->deleteCartProduct($requestProduct->id, $updateCartData->userId);
            } elseif ($requestQuantity > $currentQuantity) {
                $this->cartDataService->addCartProducts(
                    $requestProduct->id,
                    $updateCartData->userId,
                    $requestQuantity - $currentQuantity
                );
            } elseif ($requestQuantity > 0 && $requestQuantity < $currentQuantity) {
                $this->cartDataService->deleteCartProduct(
                    $requestProduct->id,
                    $updateCartData->userId,
                    $currentQuantity - $requestQuantity
                );
            }
        }
    }
}

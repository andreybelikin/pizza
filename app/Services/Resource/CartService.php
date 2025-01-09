<?php

namespace App\Services\Resource;

use App\Dto\Request\GetCartData;
use App\Dto\Request\UpdateCartData;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Services\DBTransactionService;
use App\Services\Limit\CartLimitService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CartService
{
    public function __construct(
        private CartLimitService $cartLimitService,
        private CartDataService $cartDataService,
        private DBTransactionService $dbTransactionService
    ) {}

    public function getCart(string $userId): JsonResource
    {
        $getCartData = new GetCartData($userId);
        Gate::authorize('get', [CartProduct::class, $getCartData->userId]);

        return $this->getCartResource($getCartData->userId);
    }

    public function updateCart(CartUpdateRequest $request): JsonResource
    {
        $updateCartData = UpdateCartData::fromRequest($request);
        Gate::authorize('update', [CartProduct::class, $updateCartData->userId]);
        $this->cartLimitService->checkQuantityPerTypeLimit($updateCartData->products);

        $this->dbTransactionService->execute(function () use ($updateCartData) {
            $this->updateCartByRequestProducts($updateCartData);
        });

        return $this->getCartResource($updateCartData->userId);
    }

    public function deleteCart(string $userId): void
    {
        Gate::authorize('delete', [CartProduct::class, $userId]);
        $this->dbTransactionService->execute(function () use ($userId) {
            $this->cartDataService->deleteCart($userId);
        });
    }

    private function getCartResource(int $userId): JsonResource
    {
        $cartData = $this->cartDataService->getCart($userId);
        return new CartResource($cartData);
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
                ->where('product_id', $requestProductId)
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

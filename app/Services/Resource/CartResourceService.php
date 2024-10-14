<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProduct;
use App\Models\Product;
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

    public function getCart(): JsonResource
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();

        return $this->buildCartResource();
    }

    public function updateCart(CartUpdateRequest $request): JsonResource
    {
//        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();
        $requestProducts = $request->input('products');
        $this->cartLimitService->checkQuantityPerTypeLimit($requestProducts);
        $cartProducts = CartProduct::getCartDistinctProducts();
        $this->updateCartByRequestProducts($requestProducts, $cartProducts);

        return $this->buildCartResource();
    }

    public function deleteCart(): void
    {
        $this->ensureCartExists();
        $this->ensureUserIsCartOwner();
        CartProduct::emptyCart();
    }

    private function addProductToCart(int $productId, int $quantity): void
    {
        $preparedProducts = [];

        for ($i = 0; $i < $quantity; $i++) {
            $preparedProducts[] = [
                'product_id' => $productId,
                'user_id' => $this->cartUserId,
            ];
        }

        CartProduct::addProductsToCart($preparedProducts);
    }

    private function buildCartResource(): JsonResource
    {
        $cartProducts = CartProduct::getCartDistinctProducts();
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

    private function updateCartByRequestProducts(array $requestProducts, array $cartProducts): void
    {
        foreach ($requestProducts as $requestProduct) {
            $requestProductId = $requestProduct['id'];
            $requestProductQuantity = $requestProduct['quantity'];

            $cartProductMatch = array_filter(
                $cartProducts,
                fn ($cartProduct) => $cartProduct['id'] === $requestProductId
            );

            if (empty($cartProductMatch)) {
                $this->addProductToCart($requestProductId, $requestProductQuantity);
                continue;
            }

            $newProductQuantity = $requestProductQuantity - $cartProductMatch['quantity'];

            match ($newProductQuantity) {
                0 => CartProduct::deleteCartProduct($requestProductId),
                $newProductQuantity > 0 => $this->addProductToCart($requestProductId, $newProductQuantity),
                $newProductQuantity < 0 => CartProduct::deleteCartProduct($requestProductId, abs($newProductQuantity))
            };
        }
    }
}

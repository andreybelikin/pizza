<?php

namespace App\Services\Limit;

use App\Enums\Limit\Cart\LimitedProductType;
use App\Models\CartProduct;
use App\Models\Product;

class QuantityPerTypeLimitCheck
{
    public function __construct(
        private array $requestProducts,
        private array $cartProducts
    ) {}

    public function setProducts(array $requestProducts): self
    {
        $this->requestProducts = $requestProducts;
        $this->cartProducts = $this->getCartDistinctProducts();

        return $this;
    }

    public function check(): void
    {
        $productsWithType = $this->getProductsWithTypes();

        foreach (LimitedProductType::cases() as $limitedType) {
            $this->checkProductsPerTypeQuantity($productsWithType, $limitedType);
        }
    }

    private function getProductsWithTypes(): array
    {
        $productsIds = array_column([
            ...$this->requestProducts,
            ...$this->cartProducts,
            ], 'id')
        ;
        $productsIds = array_unique($productsIds);

        return Product::getProductsTypes($productsIds, LimitedProductType::getTypes());
    }

    private function getCartDistinctProducts(): array
    {
        $cartUserId = auth()->user()->getAuthIdentifier();
        return CartProduct::getCartDistinctProducts($cartUserId);
    }

    private function checkProductsPerTypeQuantity(array $productsWithType, LimitedProductType $limitedType): void
    {
        $productsQuantity = 0;

        foreach ($productsWithType as $productWithType) {
            if ($productWithType['type'] === $limitedType->getName()) {
                $productsQuantity += $this->getQuantityById($productWithType['id']);
            }
        }

        if ($productsQuantity > $limitedType->value) {
            // исключение
        }
    }

    private function getQuantityById(string $productId): int
    {
        $quantity = 0;
        $products = [...$this->requestProducts, ...$this->cartProducts];

        foreach ($products as $product) {
            if ($product['id'] === $productId) {
                $quantity += $product['quantity'];
            }
        }

        return $quantity;
    }
}

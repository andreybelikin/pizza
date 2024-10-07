<?php

namespace App\Services\Limit;

use App\Enums\Limit\Cart\LimitedProductType;
use App\Exceptions\Resource\Cart\QuantityPerTypeLimitException;
use App\Models\CartProduct;
use App\Models\Product;

class QuantityPerTypeLimitCheck
{
    private array $requestProducts;
    private array $cartProducts;

    public function setProducts(array $requestProducts): self
    {
        $this->requestProducts = $requestProducts;
        $this->cartProducts = $this->getCartDistinctProducts();

        return $this;
    }

    public function check(): void
    {
        $violatedLimits = [];

        foreach (LimitedProductType::cases() as $limitedType) {
            $productsQuantity = $this->checkProductsPerTypeQuantity($limitedType);

            if ($productsQuantity > $limitedType->value) {
                $violatedLimits[] = sprintf(
                    'Products quantity of type %s must not be more than %s',
                    $limitedType->getName(),
                    $limitedType->value)
                ;
            }
        }

        if (!empty($violatedLimits)) {
            throw new QuantityPerTypeLimitException($violatedLimits);
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

    private function checkProductsPerTypeQuantity(LimitedProductType $limitedType): int
    {
        $productsQuantity = 0;
        $productsWithType = $this->getProductsWithTypes();

        foreach ($productsWithType as $productWithType) {
            if ($productWithType['type'] === $limitedType->getName()) {
                $productsQuantity += $this->getQuantityById($productWithType['id']);
            }
        }

        return $productsQuantity;
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

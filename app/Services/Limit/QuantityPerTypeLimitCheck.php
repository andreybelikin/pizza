<?php

namespace App\Services\Limit;

use App\Enums\Limit\Cart\CartProductLimit;
use App\Exceptions\Resource\Cart\QuantityPerTypeLimitException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class QuantityPerTypeLimitCheck
{
    private array $requestProducts;

    public function setProducts(array $requestProducts): self
    {
        $this->requestProducts = $requestProducts;

        return $this;
    }

    public function check(): void
    {
        $productsTypes = $this->getProductsTypes();

        foreach (CartProductLimit::cases() as $limit) {
            $productsQuantity = $this->checkProductsPerTypeQuantity($limit, $productsTypes);

            if ($productsQuantity > $limit->value) {
                $violatedLimits[] = sprintf(
                    'Products quantity of type %s must not be more than %s',
                    $limit->getName(),
                    $limit->value)
                ;
            }
        }

        if (!empty($violatedLimits)) {
            throw new QuantityPerTypeLimitException($violatedLimits);
        }
    }

    private function getProductsTypes(): Collection
    {
        $ids = array_column($this->requestProducts, 'id');

        return Product::getProductsTypes($ids);
    }

    private function checkProductsPerTypeQuantity(CartProductLimit $limitedType, Collection $productsTypes): int
    {
        $productsQuantity = 0;

        foreach ($this->requestProducts as $product) {
            $productType = $productsTypes->firstWhere('id', $product['id']);

            if ($productType === $limitedType->getName() && $product['quantity'] > 0) {
                $productsQuantity += $product['quantity'];
            }
        }

        return $productsQuantity;
    }
}

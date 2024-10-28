<?php

namespace App\Services\Limit;

use App\Enums\Limit\Cart\CartProductLimit;
use App\Exceptions\Limit\QuantityPerTypeLimitException;
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
        $products = $this->getProductsTypes();

        foreach (CartProductLimit::cases() as $limit) {
            $productsQuantity = $this->getQuantityByType($limit, $products);

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

    private function getQuantityByType(CartProductLimit $limitedType, Collection $products): int
    {
        $productsQuantity = 0;

        foreach ($this->requestProducts as $requestProduct) {
            $product = $products->firstWhere('id', $requestProduct['id']);

            if ($product->type === $limitedType->getName()) {
                $productsQuantity += $requestProduct['quantity'];
            }
        }

        return $productsQuantity;
    }
}

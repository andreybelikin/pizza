<?php

namespace App\Services\Limit;

use App\Enums\ProductType;
use App\Http\Requests\Cart\CartAddRequest;
use App\Models\Product;

class QuantityPerTimeLimitCheck
{
    private array $products;

    public function setCartProducts(CartAddRequest $request): self
    {
        $this->products = $request->input('products');

        return $this;
    }

    private function check(): void
    {
        foreach (ProductType::getTypes() as $type) {
            $quantityInRequest = $this->getRequestProductsQuantity($type);
        }
    }

    private function getCartProductsQuantity()
    {

    }

    private function getRequestProductsQuantity(string $type): int
    {
        $ids = array_column($this->products, 'id');

        return Product::getProductsQuantityByType($ids, $type);
    }
}

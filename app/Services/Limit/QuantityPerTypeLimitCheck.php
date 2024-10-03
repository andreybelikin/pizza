<?php

namespace App\Services\Limit;

use App\Enums\Limit\Cart\LimitedProductTypes;
use App\Enums\ProductType;
use App\Http\Requests\Cart\CartAddRequest;
use App\Models\Product;

class QuantityPerTypeLimitCheck
{
    private array $requestProducts;

    public function setRequestProducts(CartAddRequest $request): self
    {
        $this->requestProducts = $request->input('products');

        return $this;
    }

    private function check(): void
    {
        foreach (LimitedProductTypes::getTypes() as $type) {
            $typeLimit = LimitedProductTypes::getLimit($type);
            $quantity = $this->getProductsPerTypeQuantity($type);

            if ($quantity > $typeLimit) {

            }
        }
    }

    private function getProductsPerTypeQuantity(string $type): int
    {
        $requestProductsPerType = $this->getRequestProductsPerTypes();

        $cartDistinctProductsIds = $this->getCartDistinctProductsIds();
        $productsIds = [...$this->requestProducts, ...$cartDistinctProductsIds];

        return  $this->getTotalNumber($productsIds, $type);
    }

    private function getRequestProductsQuantityPerType(string $type): int
    {
        $quantityPerType = 0;

        foreach ($requestProductsIdsPerType as $value) {

            foreach ()
        }
    }

    private function getRequestProductsPerTypes(): array
    {
        $ids = array_column($this->requestProducts, 'id');
    }

    private function getCartProductsPerTypes(): array
    {

    }

    private function getProductsTypes(array $productsIds): array
    {
        return Product::getProductsTypes($productsIds, LimitedProductTypes::getTypes());
    }

    private function getCartDistinctProductsIds(): array
    {
        return Product::getCartDistinctProductsIds();
    }

    private function getTotalNumber(array $productsIds, string $type): int
    {
        return Product::getProductsQuantityByType($productsIds, $type);
    }
}

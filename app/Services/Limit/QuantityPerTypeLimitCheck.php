<?php

namespace App\Services\Limit;

use App\Enums\Limit\CartProductLimit;
use App\Exceptions\Limit\QuantityPerTypeLimitException;
use App\Services\Resource\ProductDataService;
use Illuminate\Support\Collection;

class QuantityPerTypeLimitCheck
{
    public function __construct(private ProductDataService $productDataService)
    {}

    public function check(Collection $requestProducts): void
    {
        $productsModels = $this->productDataService->getProductsTypes($requestProducts->pluck('id'));

        foreach (CartProductLimit::cases() as $limit) {
            $productsQuantity = $this->getQuantityByType($limit, $requestProducts, $productsModels);

            if ($productsQuantity > $limit->value) {
                $violatedLimits[] = sprintf(
                    'Products quantity of type %s must not be more than %s',
                    $limit->getName(),
                    $limit->value);
            }
        }

        if (!empty($violatedLimits)) {
            throw new QuantityPerTypeLimitException($violatedLimits);
        }
    }

    private function getQuantityByType(
        CartProductLimit $limitedType,
        Collection $requestProducts,
        Collection $productsModels
    ): int {
        $productsQuantity = 0;

        foreach ($requestProducts as $requestProduct) {
            $productModel = $productsModels->firstWhere('id', $requestProduct->id);

            if ($productModel->type === $limitedType->getName()) {
                $productsQuantity += $requestProduct->quantity;
            }
        }

        return $productsQuantity;
    }
}

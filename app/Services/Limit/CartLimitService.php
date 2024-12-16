<?php

namespace App\Services\Limit;

class CartLimitService
{
    public function __construct(private QuantityPerTypeLimitCheck $quantityPerTypeLimitCheck)
    {}

    public function checkQuantityPerTypeLimit(array $requestProducts): void
    {
        $this->quantityPerTypeLimitCheck
            ->setProducts($requestProducts)
            ->check();
    }
}

<?php

namespace App\Services\Limit;

class CartLimitService
{
    public function __construct(private QuantityPerTypeLimitCheck $quantityPerTimeLimit)
    {}

    public function checkQuantityPerTypeLimit(array $requestProducts): void
    {
        $this->quantityPerTimeLimit
            ->setProducts($requestProducts)
            ->check()
        ;
    }
}

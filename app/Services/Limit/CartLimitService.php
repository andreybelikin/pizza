<?php

namespace App\Services\Limit;

use Illuminate\Support\Collection;

class CartLimitService
{
    public function __construct(private QuantityPerTypeLimitCheck $quantityPerTypeLimitCheck)
    {}

    public function checkQuantityPerTypeLimit(Collection $requestCartProducts): void
    {
        $this->quantityPerTypeLimitCheck->check($requestCartProducts);
    }
}

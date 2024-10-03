<?php

namespace App\Services\Limit;

use App\Http\Requests\Cart\CartAddRequest;

class CartLimitService
{
    public function __construct(private QuantityPerTypeLimitCheck $quantityPerTimeLimit)
    {}

    public function checkQuantityPerTypeLimit(CartAddRequest $request): void
    {
        $this->quantityPerTimeLimit
            ->setRequestProducts($request)
            ->check()
        ;
    }
}

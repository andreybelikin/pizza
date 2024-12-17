<?php

namespace App\Dto;

use Illuminate\Support\Collection;

class CartData {
    public function __construct(
        /** @var Collection<CartProductsData> */
        public Collection $cartProducts,
        public int $totalSum
    ) {}
}

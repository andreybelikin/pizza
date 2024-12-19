<?php

namespace App\Dto;

use Illuminate\Support\Collection;

class CartData {
    public function __construct(
        /** @var Collection<CartProductData> */
        public Collection $products,
        public int $totalSum
    ) {}
}

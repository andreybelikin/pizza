<?php

namespace App\Dto\Request;

readonly class UpdateCartProductData
{
    public function __construct(
        public int $id,
        public int $quantity
    ) {}
}

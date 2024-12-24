<?php

namespace App\Dto\Request;

class UpdateCartProductData
{
    public function __construct(
        public int $id,
        public int $quantity
    ) {}
}

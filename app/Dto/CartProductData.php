<?php

namespace App\Dto;

class CartProductData
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $type,
        public int $price,
        public int $quantity,
        public int $totalPrice
    ) {}
}

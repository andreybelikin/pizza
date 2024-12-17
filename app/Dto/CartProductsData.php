<?php

namespace App\Dto;

use Illuminate\Support\Collection;

class CartProductsData
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

    public static function createFromDB(Collection $cartProductsEntries): Collection
    {
        return $cartProductsEntries->map(function ($cartProductsEntry) {
            return new self(
                id: $cartProductsEntry->id,
                title: $cartProductsEntry->title,
                description: $cartProductsEntry->description,
                type: $cartProductsEntry->type,
                price: $cartProductsEntry->price,
                quantity: $cartProductsEntry->quantity,
                totalPrice: $cartProductsEntry->totalPrice
            );
        });
    }
}

<?php

namespace App\Dto;

use Illuminate\Support\Collection;

readonly class OrderProductData
{
    public function __construct(
        public int $id,
        public int $quantity,
        public ?string $title,
        public ?string $description,
        public ?string $type,
        public ?int $price,
    ) {}

    public static function fromRequest(?array $requestProducts): ?Collection
    {
        if (is_null($requestProducts)) {
            return null;
        }

        return collect(
            array_map(function ($product) {
                return new self(
                    id: $product['id'],
                    quantity: $product['quantity'],
                    title: $product['title'] ?? null,
                    description: $product['description'] ?? null,
                    type: $product['type'] ?? null,
                    price: $product['price'] ?? null,
                );
            }, $requestProducts)
        );
    }

    public static function fromCartProducts(Collection $cartProducts): Collection
    {
        return $cartProducts->map(function($product) {
            return new self(
                id: $product->id,
                quantity: $product->quantity,
                title: $product->title,
                description: $product->description,
                type: $product->type,
                price: $product->price,
            );
        });
    }

    public function getProductArray(): array
    {
        $orderInfo = [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'price' => $this->price,
        ];

        return array_filter($orderInfo, fn($value) => !is_null($value));
    }
}

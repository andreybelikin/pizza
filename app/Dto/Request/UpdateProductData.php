<?php

namespace App\Dto\Request;

use App\Http\Requests\Product\ProductUpdateRequest;

readonly class UpdateProductData
{
    public function __construct(
        public int $id,
        public ?string $title,
        public ?string $description,
        public ?string $type,
        public ?int $price,
    ) {}

    public static function fromRequest(ProductUpdateRequest $request): self
    {
        return new self(
            id: $request->route('product') ? (int)$request->route('product') : null,
            title: $request->get('title'),
            description: $request->get('description'),
            type: $request->get('type'),
            price: $request->get('price'),
        );
    }

    public function getProductInfo(): array
    {
        return array_filter((array)$this, fn($value) => !is_null($value));
    }
}

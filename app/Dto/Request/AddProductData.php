<?php

namespace App\Dto\Request;

use App\Http\Requests\Product\ProductAddRequest;

readonly class AddProductData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $type,
        public int $price
    ) {}

    public static function fromRequest(ProductAddRequest $request): self
    {
        return new self(
            $request->get('title'),
            $request->get('description'),
            $request->get('type'),
            (int)$request->get('price'),
        );
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}

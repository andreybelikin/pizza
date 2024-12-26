<?php

namespace App\Dto\Request;

use App\Http\Requests\Product\ProductIndexRequest;

readonly class ListProductFilterData
{
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?string $type,
        public ?int $minPrice,
        public ?int $maxPrice,
    ) {}

    public static function fromRequest(ProductIndexRequest $request): self
    {
        return new self(
            title: $request->get('title') ?? null,
            description: $request->get('description') ?? null,
            type: $request->get('type') ?? null,
            minPrice: $request->get('minPrice') ?? null,
            maxPrice: $request->get('maxPrice') ?? null,
        );
    }
}

<?php

namespace App\Dto\Request;

use App\Http\Requests\Order\OrdersRequest;

readonly class ListOrderFilterData
{
    public function __construct(
        public ?int $userId,
        public ?string $productTitle,
        public ?int $minTotal,
        public ?int $maxTotal,
        public ?string $status,
        public ?string $createdAt
    ) {}

    public static function createFromRequest(OrdersRequest $request): self
    {
        $createdAt = $request->get('createdAt');

        return new self(
            userId: (int)$request->get('userId') ?? null,
            productTitle: $request->get('productTitle') ?? null,
            minTotal: (int)$request->get('minTotal') ?? null,
            maxTotal: (int)$request->get('maxTotal') ?? null,
            status: $request->get('status') ?? null,
            createdAt: !is_null($createdAt)
                ? \DateTime::createFromFormat('d.m.Y', $createdAt)->format('Y-m-d')
                : null
        );
    }
}

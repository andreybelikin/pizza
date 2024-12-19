<?php

namespace App\Dto\Request;

use App\Dto\OrderProductData;
use App\Http\Requests\OrderAddRequest;
use Illuminate\Support\Collection;

readonly class NewOrderData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $address,
        public ?string $status,
        public int $userId,
        /** @var Collection<OrderProductData> $orderProducts */
        public Collection $orderProducts,
    ) {}

    public static function create(
        OrderAddRequest $request,
        Collection $orderProducts,
    ): self {
        return new self(
            name: $request->get('name'),
            phone: $request->get('phone'),
            address: $request->get('address') ?? null,
            status: $request->get('status') ?? null,
            userId: (int)$request->route('userId'),
            orderProducts: $orderProducts,
        );
    }
}

<?php

namespace App\Dto\Request;

use App\Dto\OrderProductData;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Support\Collection;

readonly class UpdateOrderData
{
    public function __construct(
        public ?string $name,
        public ?string $phone,
        public ?string $address,
        public ?string $status,
        public ?string $type,
        public ?int $total,
        /** @var null|Collection<OrderProductData> $orderProducts */
        public ?Collection $orderProducts
    ) {}

    public static function create(
        OrderUpdateRequest $request,
        ?Collection $orderProducts,
        ?int $total
    ): self {
        return new self(
            name: $request->get('name'),
            phone: $request->get('phone'),
            address: $request->get('address'),
            status: $request->get('status'),
            type: $request->get('orderProducts'),
            total: $total,
            orderProducts: $orderProducts,
        );
    }

    public function getOderInfo(): array
    {
        $orderInfo = [
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'type' => $this->type,
            'total' => $this->total,
        ];

        return array_filter($orderInfo, fn($value) => !is_null($value));
    }
}

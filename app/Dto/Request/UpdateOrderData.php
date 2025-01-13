<?php

namespace App\Dto\Request;

use App\Dto\OrderProductData;
use App\Http\Requests\Order\OrderUpdateRequest;
use Illuminate\Support\Collection;

readonly class UpdateOrderData
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $phone,
        public ?string $address,
        public ?string $status,
        public ?string $type,
        /** @var null|Collection<OrderProductData> $orderProducts */
        public ?Collection $orderProducts
    ) {}

    public static function create(
        OrderUpdateRequest $request,
        ?Collection $orderProducts,
    ): self {
        return new self(
            id: $request->route('order'),
            name: $request->get('name') ?? null,
            phone: $request->get('phone') ?? null,
            address: $request->get('address') ?? null,
            status: $request->get('status') ?? null,
            type: $request->get('type') ?? null,
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
        ];

        return array_filter($orderInfo, fn($value) => !is_null($value));
    }
}

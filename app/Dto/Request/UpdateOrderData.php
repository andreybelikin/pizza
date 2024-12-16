<?php

namespace App\Dto\Request;

use App\Http\Requests\OrderUpdateRequest;

readonly class UpdateOrderData
{
    public function __construct(
        public ?string $name,
        public ?string $phone,
        public ?string $address,
        public ?string $status,
        public ?array $orderProducts,
        public ?int $total
    ) {}

    public static function fromRequest(OrderUpdateRequest $request): self
    {
        return new self(
            $request->get('name'),
            $request->get('phone'),
            $request->get('address'),
            $request->get('status'),
            $request->get('orderProducts'),
            $request->get('orderProducts')
                ? array_sum(array_column($request->get('orderProducts'), 'totalPrice'))
                : null
        );
    }

    public function getOderInfo(): array
    {
        $orderInfo = [
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'total' => $this->total,
        ];
        return array_filter($orderInfo, fn($value) => !is_null($value));
    }
}

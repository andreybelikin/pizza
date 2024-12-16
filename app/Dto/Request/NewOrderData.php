<?php

namespace App\Dto\Request;

readonly class NewOrderData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $address,
        public ?string $status,
        public ?array $orderProducts,
        public ?int $total
    ) {}
}

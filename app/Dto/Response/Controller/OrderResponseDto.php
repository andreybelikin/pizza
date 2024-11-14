<?php

namespace App\Dto\Response\Controller;

readonly class OrderResponseDto
{
    public function __construct(
        string $title,
        string $phone,
        string $address,
        string $status,
        array $products,
        string $total
    ) {}
}

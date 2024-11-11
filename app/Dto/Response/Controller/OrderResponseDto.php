<?php

namespace App\Dto\Response\Controller;

use Illuminate\Http\Response;

class OrderResponseDto
{
    public function __construct(
        readonly string $title,
        readonly string $phone,
        readonly string $address,
        readonly string $status,
        readonly array $products,
        readonly string $total
    ) {}
}

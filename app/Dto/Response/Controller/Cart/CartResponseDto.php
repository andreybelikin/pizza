<?php

namespace App\Dto\Response\Controller\Cart;
class CartResponseDto
{
    private int $status;

    public function __construct(
        public readonly string $message,
        public readonly array  $violatedRestrictions,
        public readonly array  $addedProducts,
    ) {}
}

<?php

namespace App\Dto\Response\Resourse;

class CartLimitExceptionResponseDto
{
    private const MESSAGE = 'Cart change error';

    public function __construct(private array $violations)
    {}

    public function toArray(): array
    {
        return [
            'message' => self::MESSAGE,
            'violations' => $this->violations,
        ];
    }
}

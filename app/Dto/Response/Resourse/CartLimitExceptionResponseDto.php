<?php

namespace App\Dto\Response\Resourse;

use Illuminate\Http\Response;

class CartLimitExceptionResponseDto
{
    private const MESSAGE = 'Cart change error';
    public const STATUS = Response::HTTP_BAD_REQUEST;

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

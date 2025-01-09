<?php

namespace App\Dto\Response\Middleware;

use Illuminate\Http\Response;

class TokenExceptionResponseDto
{
    public const STATUS = Response::HTTP_UNAUTHORIZED;

    public function __construct(public string $message)
    {}

    public function toArray(): array
    {
        return ['message' => $this->message];
    }
}

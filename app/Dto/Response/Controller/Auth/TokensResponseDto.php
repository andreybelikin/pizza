<?php

namespace App\Dto\Response\Controller\Auth;

use Symfony\Component\HttpFoundation\Response;

class TokensResponseDto
{
    public const STATUS = Response::HTTP_OK;

    public function __construct(
        private readonly string $accessToken,
        private readonly string $refreshToken
    ) {}

    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
        ];
    }
}

<?php

namespace App\Dto\Response\Controller\Auth;

use Illuminate\Http\Response;

class InvalidCredentialsResponseDto
{
    public const STATUS = Response::HTTP_UNAUTHORIZED;
    private const MESSAGE = 'User with these credentials is not exist';

    public function toArray(): array
    {
        return ['message' => self::MESSAGE];
    }
}

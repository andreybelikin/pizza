<?php

namespace App\Dto\Response\Controller\Auth;

use Illuminate\Http\Response;

class LogoutResponseDto
{
    public const STATUS = Response::HTTP_OK;
    private const MESSAGE = 'Successfully logged out';

    public function toArray(): array
    {
        return ['message' => self::MESSAGE];
    }
}

<?php

namespace App\Dto\Response\HttpMiddleware;

use Illuminate\Http\Response;

class NotAdminResponseDto
{
    public const STATUS = Response::HTTP_UNAUTHORIZED;
    public const MESSAGE = 'User has not admin permission';

    public function toArray(): array
    {
        return ['message' => self::MESSAGE];
    }
}

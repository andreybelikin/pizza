<?php

namespace App\Dto\Response;

use Illuminate\Http\Response;

class InternalErrorResponseDto
{
    public const MESSAGE = 'Something went wrong. Try again later';
    public const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;
    public function toArray(): array
    {
        return ['message' => static::MESSAGE];
    }
}

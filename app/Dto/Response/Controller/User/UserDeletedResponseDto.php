<?php

namespace App\Dto\Response\Controller\User;
use Illuminate\Http\Response;

class UserDeletedResponseDto
{
    public const STATUS = Response::HTTP_OK;
    private const MESSAGE = 'User deleted';

    public function toArray(): array
    {
        return ['message' => self::MESSAGE];
    }
}

<?php

namespace App\Dto\Response\Controller\Auth;

use Illuminate\Http\Response;

class RegisterResponceDto
{
    public const STATUS = Response::HTTP_OK;
    private const MESSAGE = 'User successfully registered';

    public function toArray(): array
    {
        return ['message' => self::MESSAGE];
    }
}

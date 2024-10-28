<?php

namespace App\Exceptions\Auth;

use Symfony\Component\HttpFoundation\Response;

class TokenUserNotDefinedException extends TokenException
{
    private CONST MESSAGE = 'Token user not found';
    private CONST STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

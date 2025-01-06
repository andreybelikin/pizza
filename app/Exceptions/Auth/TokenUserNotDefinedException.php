<?php

namespace App\Exceptions\Auth;

class TokenUserNotDefinedException extends TokenException
{
    private const MESSAGE = 'Token user not found';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

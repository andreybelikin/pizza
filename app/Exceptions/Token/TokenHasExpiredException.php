<?php

namespace App\Exceptions\Token;

class TokenHasExpiredException extends TokenException
{
    private CONST MESSAGE = 'Token has expired';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

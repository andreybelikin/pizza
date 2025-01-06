<?php

namespace App\Exceptions\Auth;

class TokenBlacklistedException extends TokenException
{
    private CONST MESSAGE = 'The token has been blacklisted';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

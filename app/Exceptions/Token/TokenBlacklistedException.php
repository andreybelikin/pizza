<?php

namespace App\Exceptions\Token;

use Symfony\Component\HttpFoundation\Response;

class TokenBlacklistedException extends TokenException
{
    private CONST MESSAGE = 'The token has been blacklisted';
    private CONST STATUS = Response::HTTP_UNAUTHORIZED;
    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

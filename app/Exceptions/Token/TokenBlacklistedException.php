<?php

namespace App\Exceptions\Token;

use App\Exceptions\Token\TokenException;
use Symfony\Component\HttpFoundation\Response;

class TokenBlacklistedException extends TokenException
{
    private CONST MESSAGE = 'Passed token is in blacklist';
    private CONST STATUS = Response::HTTP_UNAUTHORIZED;
    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

<?php

namespace App\Exceptions\Token;

use Symfony\Component\HttpFoundation\Response;

class TokenAbsenceException extends TokenException
{
    private CONST MESSAGE = 'Nor access or refresh token passed';
    private CONST STATUS = Response::HTTP_UNAUTHORIZED;
    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

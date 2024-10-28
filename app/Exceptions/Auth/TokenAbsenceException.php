<?php

namespace App\Exceptions\Auth;

use Symfony\Component\HttpFoundation\Response;

class TokenAbsenceException extends TokenException
{
    private CONST MESSAGE = 'Nor access or refresh token passed';
    private CONST STATUS = Response::HTTP_BAD_REQUEST;
    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

<?php

namespace App\Exceptions\Auth;

class TokenAbsenceException extends TokenException
{
    private CONST MESSAGE = 'Nor access or refresh token passed';
    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

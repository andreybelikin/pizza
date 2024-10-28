<?php

namespace App\Exceptions\Auth;

class TokenIsInvalidException extends TokenException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Exceptions\Token;

class TokenIsInvalidException extends TokenException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Exceptions;

class InvalidCredentialsException extends \Exception
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}

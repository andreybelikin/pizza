<?php

namespace App\Exceptions\Limit;

class QuantityPerTypeLimitException extends CartLimitException
{
    public function __construct(public array $violations)
    {
        parent::__construct();
    }
}

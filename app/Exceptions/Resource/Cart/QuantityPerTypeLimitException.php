<?php

namespace App\Exceptions\Resource\Cart;

use App\Exceptions\Resource\ResourceException;
use Symfony\Component\HttpFoundation\Response;

class QuantityPerTypeLimitException extends \Exception
{
    public function __construct(public array $violations)
    {
        parent::__construct();
    }
}

<?php

namespace App\Exceptions\Resource;

use Symfony\Component\HttpFoundation\Response;

class ResourceUpdateException extends ResourceException
{
    private const STATUS = Response::HTTP_BAD_REQUEST;

    public function __construct($message)
    {
        parent::__construct($message, self::STATUS);
    }
}

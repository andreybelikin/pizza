<?php

namespace App\Exceptions\Resource;

use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends ResourceException
{
    private const MESSAGE = 'Resource is not exist';
    private const STATUS = Response::HTTP_NOT_FOUND;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

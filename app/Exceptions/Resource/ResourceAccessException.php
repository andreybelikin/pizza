<?php

namespace App\Exceptions\Resource;

use Symfony\Component\HttpFoundation\Response;

class ResourceAccessException extends ResourceException
{
    private const MESSAGE = 'Don\'t have permission to this resource';
    private const STATUS = Response::HTTP_UNAUTHORIZED;

    public function __construct()
    {
        parent::__construct(self::MESSAGE, self::STATUS);
    }
}

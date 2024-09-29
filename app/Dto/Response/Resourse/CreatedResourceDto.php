<?php

namespace App\Dto\Response\Resourse;

use Symfony\Component\HttpFoundation\Response;

class CreatedResourceDto
{
    public const STATUS = Response::HTTP_CREATED;

    public function toArray(): array
    {
        return ['message' => 'Resource successfully added'];
    }
}

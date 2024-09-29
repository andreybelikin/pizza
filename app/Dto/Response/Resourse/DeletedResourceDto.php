<?php

namespace App\Dto\Response\Resourse;

use Symfony\Component\HttpFoundation\Response;

class DeletedResourceDto
{
    public const STATUS = Response::HTTP_OK;

    public function toArray(): array
    {
        return ['message' => 'Successfully deleted'];
    }
}

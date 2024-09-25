<?php

namespace App\Dto\Response\Resourse;

use Symfony\Component\HttpFoundation\Response;

class AddedResourceDto
{
    public function __construct(private readonly string $resourceName)
    {}

    public const STATUS = Response::HTTP_CREATED;

    public function toArray(): array
    {
        return ['message' => $this->resourceName . ' was added'];
    }
}

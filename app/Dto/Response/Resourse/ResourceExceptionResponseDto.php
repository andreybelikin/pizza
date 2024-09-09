<?php

namespace App\Dto\Response\Resourse;

class ResourceExceptionResponseDto
{
    public function __construct(private readonly string $message, public int $status)
    {}

    public function toArray(): array
    {
        return ['message' => $this->message];
    }
}

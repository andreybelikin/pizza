<?php

namespace Auth\Register;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Response;

class SuccessRegisterResponseDto implements Arrayable
{
    public const STATUS = Response::HTTP_OK;
    public function __construct(
        private string $jwtToken
    ) {}

    public function toArray(): array
    {
        return ['jwtToken' => $this->jwtToken];
    }
}

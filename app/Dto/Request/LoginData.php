<?php

namespace App\Dto\Request;

use App\Http\Requests\Auth\LoginRequest;

readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            $request->get('email'),
            $request->get('password'),
        );
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}

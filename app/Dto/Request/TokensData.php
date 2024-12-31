<?php

namespace App\Dto\Request;

use Illuminate\Http\Request;

readonly class TokensData
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->bearerToken(),
            $request->header('x-refresh-token'),
        );
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}

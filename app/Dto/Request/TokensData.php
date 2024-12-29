<?php

namespace App\Dto\Request;

use App\Http\Requests\TokensRequest;

readonly class TokensData
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
    ) {}

    public static function fromRequest(TokensRequest $request): self
    {
        return new self(
            $request->get('accessToken'),
            $request->get('refreshToken'),
        );
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}

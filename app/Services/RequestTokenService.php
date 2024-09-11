<?php

namespace App\Services;

use App\Exceptions\Token\TokenAbsenceException;
use App\Exceptions\Token\TokenBlacklistedException;
use App\Exceptions\Token\TokenException;
use App\Exceptions\Token\TokenHasExpiredException;
use App\Exceptions\Token\TokenIsInvalidException;
use App\Exceptions\Token\TokenUserNotDefinedException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class RequestTokenService
{
    public function __construct(
        private Request $request,
        private TokenBlacklistService $blacklistService
    ) {}

    public function checkAuthorizationToken(): void
    {
        $this->checkAuthorizationTokenPresence();
        $this->checkSingleToken($this->request->bearerToken());
    }

    public function checkRefreshToken(): void
    {
        $this->checkRefreshTokenPresence();
        $this->checkSingleToken($this->request->header('x-refresh-token'));
    }

    public function checkTokensPair(): bool
    {
        $this->checkAuthorizationTokenPresence();
        $this->checkRefreshTokenPresence();
        $this->removeInvalidTokens();

        return $this->isTokensEmpty();
    }

    private function checkAuthorizationTokenPresence(): void
    {
        if (is_null($this->request->bearerToken())) {
            throw new TokenAbsenceException();
        }
    }

    private function checkRefreshTokenPresence(): void
    {
        if (!$this->request->hasHeader('x-refresh-token')
            || empty($this->request->header('x-refresh-token'))
        ) {
            throw new TokenAbsenceException();
        }

        $this->checkRefreshTokenType();
    }

    private function checkRefreshTokenType(): void
    {
        auth()->setToken($this->request->header('x-refresh-token'));

        if (!auth()->getPayload()->matches(['typ' => 'refresh'])) {
            throw new TokenAbsenceException();
        }
    }

    private function checkSingleToken(string $token): void
    {
        auth()->setToken($token);

        $this->validateToken();
        $this->isTokenBlackListed();
        $this->validateTokenUser();
    }

    private function removeInvalidTokens(): void
    {
        $tokens = [
            'authorization' => $this->request->bearerToken(),
            'x-refresh-token' => $this->request->header('x-refresh-token'),
        ];

        array_walk($tokens, function ($token, $tokenName) {
            try {
                $this->checkSingleToken($token);
            } catch (TokenException) {
                $this->request->headers->set($tokenName, '');
            }
        });
    }

    private function isTokensEmpty(): bool
    {
        return empty($this->request->bearerToken())
        && empty($this->request->header('x-refresh-token'))
            ? false
            : true;
    }

    private function validateToken(): void
    {
        try {
            auth()->getPayload();
        } catch (TokenExpiredException) {
            throw new TokenHasExpiredException();
        } catch (TokenInvalidException $exception) {
            throw new TokenIsInvalidException($exception->getMessage());
        }
    }

    private function validateTokenUser(): void
    {
        try {
            auth()->userOrFail();
        } catch (UserNotDefinedException) {
            throw new TokenUserNotDefinedException();
        }
    }

    private function isTokenBlackListed(): void
    {
        if ($this->blacklistService->isTokenBlacklisted()) {
            throw new TokenBlacklistedException();
        }
    }
}

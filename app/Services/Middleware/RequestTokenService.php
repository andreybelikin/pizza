<?php

namespace App\Services\Middleware;

use App\Exceptions\Token\TokenAbsenceException;
use App\Exceptions\Token\TokenBlacklistedException;
use App\Exceptions\Token\TokenHasExpiredException;
use App\Exceptions\Token\TokenUserNotDefinedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class RequestTokenService
{
    public function __construct(private Request $request)
    {}
    public function checkAuthorizationToken(): void
    {
        $this->checkAuthorizationTokenPresence();
        $this->isTokenBlackListed($this->request->bearerToken());
        $this->validateAuthorizationToken();
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
    }

    private function validateAuthorizationToken(): void
    {
        try {
            auth()->userOrFail();
        } catch (UserNotDefinedException) {
            throw new TokenUserNotDefinedException();
        } catch (TokenExpiredException) {
            throw new TokenHasExpiredException();
        }
    }

    private function removeInvalidTokens(): void
    {
        $tokens = [
            'authorization' => $this->request->bearerToken(),
            'x-refresh-token' => $this->request->header('x-refresh-token'),
        ];

        array_walk($tokens, function ($token, $tokenName) {
            auth()->setToken($token);

            try {
                auth()->userOrFail();
                $this->isTokenBlackListed($token);
            } catch (UserNotDefinedException | TokenExpiredException | TokenBlacklistedException $e) {
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

    private function isTokenBlackListed(string $token): void
    {
        $hashedToken = hash('sha256', $token);

        if (DB::table('token_blacklist')->where('token', $hashedToken)->exists()) {
            throw new TokenBlacklistedException();
        }
    }
}

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
    public function checkAuthorizationToken(Request $request): void
    {
        $this->checkAuthorizationTokenPresence($request);
        $this->isTokenBlackListed($request->bearerToken());
        $this->validateToken();
    }

    public function checkTokensPair(Request $request): bool
    {
        $this->checkAuthorizationTokenPresence($request);
        $this->checkRefreshTokenPresence($request);
        $this->removeInvalidTokens($request);

        return empty($request->header('authorization'))
            && empty($request->header('x-refresh-token'))
            ? false
            : true;
    }

    private function checkAuthorizationTokenPresence(Request $request): void
    {
        if (is_null($request->bearerToken())) {
            throw new TokenAbsenceException();
        }
    }

    private function checkRefreshTokenPresence(Request $request): void
    {
        if (!$request->hasHeader('x-refresh-token') || empty($request->header('x-refresh-token'))) {
            throw new TokenAbsenceException();
        }
    }

    private function validateToken(): void
    {
        try {
            auth()->userOrFail();
        } catch (UserNotDefinedException) {
            throw new TokenUserNotDefinedException();
        } catch (TokenExpiredException) {
            throw new TokenHasExpiredException();
        }
    }

    private function removeInvalidTokens(Request $request): void
    {
        $tokens = [
            'authorization' => $request->header('authorization'),
            'x-refresh-token' => $request->header('x-refresh-token'),
        ];

        array_walk($tokens, function ($token, $tokenName) use ($request) {
            auth()->setToken($token);

            try {
                $this->isTokenBlackListed($token);
                auth()->userOrFail();
            } catch (UserNotDefinedException | TokenExpiredException | TokenBlacklistedException) {
                $request->headers->set($tokenName, '');
            }
        });
    }

    private function isTokenBlackListed(string $token): void
    {
        $hashedToken = hash('sha256', $token);

        if (DB::table('token_blacklist')->where('token', $hashedToken)->exists()) {
            throw new TokenBlacklistedException();
        }
    }
}

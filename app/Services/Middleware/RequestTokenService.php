<?php

namespace App\Services\Middleware;

use App\Exceptions\Token\TokenAbsenceException;
use App\Exceptions\Token\TokenHasExpiredException;
use App\Exceptions\Token\TokenUserNotDefinedException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class RequestTokenService
{
    public function checkToken(Request $request): void
    {
        $this->checkHeaderToken($request);
        $this->validateToken();
    }

    public function checkLogoutTokens(Request $request): bool
    {
        $this->checkBodyTokens($request);
        $this->removeInvalidBodyTokens($request);

        return $request->filled('accessToken') || $request->filled('refreshToken');
    }

    private function checkHeaderToken(Request $request): void
    {
        if (is_null($request->bearerToken())) {
            throw new TokenAbsenceException();
        }
    }

    private function checkBodyTokens(Request $request): void
    {
        if (!$request->filled(['accessToken', 'refreshToken'])) {
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

    private function removeInvalidBodyTokens(Request $request): void
    {
        $tokens = [
            'accessToken' => $request->input('accessToken'),
            'refreshToken' => $request->input('refreshToken'),
        ];

        array_walk($tokens, function ($token, $tokenName) use ($request) {
            auth()->setToken($token);

            try {
                auth()->userOrFail();
            } catch (UserNotDefinedException | TokenExpiredException) {
                $request->request->remove($tokenName);
            }
        });
    }
}

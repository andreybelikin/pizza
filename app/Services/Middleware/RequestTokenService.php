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
    public function checkHeaderToken(Request $request): void
    {
        $this->checkRequestHeader($request);
        $this->validateToken();
    }

    public function checkBodyTokens(Request $request): void
    {
        $this->checkRequestBody($request);
        $this->validateBodyTokens($request);
    }

    private function checkRequestHeader(Request $request): void
    {
        if (is_null($request->bearerToken())) {
            throw new TokenAbsenceException();
        }
    }

    private function checkRequestBody(Request $request): void
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

    private function validateBodyTokens(Request $request): void
    {
        $tokens = $request->input(['accessToken', 'refreshToken']);

        array_walk($tokens, function ($token) use ($request) {
            auth()->setToken($token);
            $this->validateToken();
        });
    }
}

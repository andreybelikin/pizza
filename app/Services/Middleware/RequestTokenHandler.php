<?php

namespace App\Services\Middleware;

use App\Exceptions\TokenException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class RequestTokenHandler
{
    public static function check(Request $request): void
    {
        self::checkTokenPresence($request);
        self::validateToken($request);
    }

    private static function checkTokenPresence(Request $request): void
    {
        if (empty($request->bearerToken())) {
            throw new TokenException('Authentication header has no bearer token');
        }
    }

    private static function validateToken(): void
    {
        try {
            auth()->userOrFail();
        } catch (UserNotDefinedException) {
            throw new TokenException('Token user not found');
        } catch (TokenExpiredException) {
            throw new TokenException('Token has expired');
        }
    }
}

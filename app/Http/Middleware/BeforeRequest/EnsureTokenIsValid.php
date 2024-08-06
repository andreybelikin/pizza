<?php

namespace App\Http\Middleware\BeforeRequest;

use App\Dto\Response\HttpMiddleware\TokenErrorResponseDto;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($request->bearerToken())) {
            $response = new TokenErrorResponseDto('Authentication header has no bearer token');

            return response()->json($response->toArray(), $response::STATUS);
        }

        try {
            auth()->userOrFail();
        } catch (UserNotDefinedException) {
            $response = new TokenErrorResponseDto('User not defined');

            return response()->json($response->toArray(), $response::STATUS);
        }

        return $next($request);
    }
}

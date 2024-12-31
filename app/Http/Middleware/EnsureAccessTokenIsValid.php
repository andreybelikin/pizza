<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\TokenException;
use App\Services\Middleware\CheckTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccessTokenIsValid
{
    public function __construct(private readonly CheckTokenService $checkTokenService)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->checkTokenService->checkAccessToken();

            return $next($request);
        } catch (TokenException $exception) {
            return $exception->getResponse();
        }
    }
}

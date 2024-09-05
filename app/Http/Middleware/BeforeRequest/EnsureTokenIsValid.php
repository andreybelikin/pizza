<?php

namespace App\Http\Middleware\BeforeRequest;

use App\Exceptions\Token\TokenException;
use App\Services\RequestTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function __construct(private readonly RequestTokenService $requestTokenService)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (str_contains($request->path(), 'api/refresh')) {
                $this->requestTokenService->checkRefreshToken();
            } else {
                $this->requestTokenService->checkAuthorizationToken();
            }

            return $next($request);
        } catch (TokenException $exception) {
            return $exception->getResponse();
        }
    }
}

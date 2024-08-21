<?php

namespace App\Http\Middleware\BeforeRequest;

use App\Dto\Response\Controller\Auth\LogoutResponseDto;
use App\Exceptions\Token\TokenAbsenceException;
use App\Exceptions\Token\TokenHasExpiredException;
use App\Services\Middleware\RequestTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class EnsureTokenIsValidLogout
{
    public function __construct(private readonly RequestTokenService $requestTokenService)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$this->requestTokenService->checkLogoutTokens($request)) {
                $responseDto = new LogoutResponseDto();

                return response()->json($responseDto->toArray(), $responseDto::STATUS);
            }

            return $next($request);
        } catch (TokenAbsenceException $exception) {
            return $exception->getResponse();
        }
    }
}

<?php

namespace App\Http\Middleware;

use App\Dto\Response\Controller\Auth\LogoutResponseDto;
use App\Exceptions\Auth\TokenAbsenceException;
use App\Services\Middleware\RequestTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValidLogout
{
    public function __construct(private RequestTokenService $requestTokenService)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$this->requestTokenService->checkTokensPair()) {
                $responseDto = new LogoutResponseDto();
                return response()->json($responseDto->toArray(), $responseDto::STATUS);
            }

            return $next($request);
        } catch (TokenAbsenceException $exception) {
            return $exception->getResponse();
        }
    }
}

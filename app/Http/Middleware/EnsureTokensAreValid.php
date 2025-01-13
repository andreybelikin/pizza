<?php

namespace App\Http\Middleware;

use App\Dto\Request\TokensData;
use App\Dto\Response\Controller\Auth\LogoutResponseDto;
use App\Exceptions\Auth\TokenAbsenceException;
use App\Exceptions\Auth\TokenException;
use App\Services\Middleware\CheckTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokensAreValid
{
    public function __construct(private CheckTokenService $requestTokenService)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        $tokens = TokensData::fromRequest($request);

        try {
            $this->requestTokenService->checkTokensPair($tokens);

            return $next($request);
        } catch (TokenException $exception) {
            return $exception->getResponse();
        }
    }
}

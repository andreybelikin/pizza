<?php

namespace App\Services\Middleware;

use App\Dto\Request\TokensData;
use App\Exceptions\Auth\TokenBlacklistedException;
use App\Exceptions\Auth\TokenException;
use App\Exceptions\Auth\TokenHasExpiredException;
use App\Exceptions\Auth\TokenIsInvalidException;
use App\Exceptions\Auth\TokenUserNotDefinedException;
use App\Services\Auth\TokenBlacklistService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckTokenService
{
    public function __construct(
        private Request $request,
        private TokenBlacklistService $blacklistService
    ) {}

    public function checkAccessToken(): void
    {
        $this->checkSingleToken($this->request->bearerToken());
    }

    public function checkRefreshToken(): void
    {
        $this->checkSingleToken($this->request->header('x-refresh-token'));
    }

    public function checkTokensPair(TokensData $tokens): void
    {
        $this->checkSingleToken($tokens->accessToken);
        $this->checkSingleToken($tokens->refreshToken);
    }

    private function checkSingleToken(?string $token): void
    {
        try {
            auth()->setToken($token)->authenticate();
            $this->isTokenBlackListed();
        } catch (TokenExpiredException) {
            throw new TokenHasExpiredException();
        } catch (AuthenticationException) {
            throw new TokenUserNotDefinedException();
        } catch (TokenInvalidException $exception) {
            throw new TokenIsInvalidException($exception->getMessage());
        } catch (TokenBlacklistedException) {
            throw new TokenBlacklistedException();
        } catch (JWTException) {
            throw new TokenException('Something went wrong with the token');
        }
    }
    
    private function isTokenBlackListed(): void
    {
        if ($this->blacklistService->isTokenBlacklisted()) {
            throw new TokenBlacklistedException();
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Response\Controller\Auth\InvalidCredentialsResponseDto;
use App\Dto\Response\Controller\Auth\LogoutResponseDto;
use App\Dto\Response\Controller\Auth\RegisterResponceDto;
use App\Dto\Response\Controller\Auth\TokensResponseDto;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    use ValidatesRequests;
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): Response
    {
        try {
            [$accessToken, $refreshToken] = $this->authService->authenticateUser($request);
            $responseDto = new TokensResponseDto($accessToken, $refreshToken);
            $response = response($responseDto->toArray(), $responseDto::STATUS);
        } catch (InvalidCredentialsException) {
            $responseDto = new InvalidCredentialsResponseDto();
            $response = response($responseDto->toArray(), $responseDto::STATUS);
        }

        return $response;
    }

    public function logout(Request $request): Response
    {
        $this->authService->logoutUser($request);
        $response = new LogoutResponseDto();

        return response()->json($response->toArray(), $response::STATUS);
    }

    public function register(RegisterRequest $request): Response
    {
        $this->authService->saveNewUser($request);
        $responseDto = new RegisterResponceDto();

        return response($responseDto->toArray(), $responseDto::STATUS);
    }

    public function refresh(Request $request): Response
    {
        [$accessToken, $refreshToken] = $this->authService->refreshToken($request);
        $response = new TokensResponseDto($accessToken, $refreshToken);

        return response()->json($response->toArray(), $response::STATUS);
    }
}

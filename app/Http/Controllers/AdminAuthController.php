<?php

namespace App\Http\Controllers;

use App\Dto\Response\Controller\Auth\InvalidCredentialsResponseDto;
use App\Dto\Response\Controller\Auth\TokensResponseDto;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\TokensRequest;
use App\Services\Auth\AuthService;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController
{
    public function __construct(private readonly AuthService $authService)
    {}

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

    public function logout(TokensRequest $request): Response
    {
        $this->authService->logoutUser($request);

        return response('');
    }

    public function refresh(TokensRequest $request): Response
    {
        [$accessToken, $refreshToken] = $this->authService->refreshToken($request);
        $response = new TokensResponseDto($accessToken, $refreshToken);

        return response()->json($response->toArray(), $response::STATUS);
    }
}

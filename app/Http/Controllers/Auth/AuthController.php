<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Response\Auth\SuccessRegisterResponseDto;
use App\Dto\Response\InternalErrorResponseDto;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(): JsonResponse
    {

    }

    public function logout(): JsonResponse
    {

    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $newUserToken = $this->authService->saveNewUser($request);
            $response = new SuccessRegisterResponseDto($newUserToken);
        } catch (Exception $exception) {
            dd($exception);
            $response = new InternalErrorResponseDto();
        }
        return response()->json($response->toArray(), $response::STATUS);
    }
}

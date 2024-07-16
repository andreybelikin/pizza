<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
            $this->authService->saveNewUser($request);
        } catch (Exception) {
            return response()->json('Internal error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

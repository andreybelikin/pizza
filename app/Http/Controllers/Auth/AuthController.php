<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    use ValidatesRequests;
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(AuthenticateRequest $request): Response
    {
        try {
            $newToken = $this->authService->authenticateUser($request);
            $response = response()->header('Authorization', $newToken);
        } catch (InvalidCredentialsException $exception) {
            $response = response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return $response;
    }

    public function logout(): Response
    {
        $this->authService->logoutUser();

        return response();
    }

    public function register(RegisterRequest $request): Response
    {
        $newUserToken = $this->authService->saveNewUser($request);

        return response()->header('Authorization', $newUserToken);
    }
}

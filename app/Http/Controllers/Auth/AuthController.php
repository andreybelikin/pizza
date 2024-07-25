<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Response\Auth\SuccessRegisterResponseDto;
use App\Dto\Response\InternalErrorResponseDto;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController
{
    use ValidatesRequests;
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
            $responseDto = new SuccessRegisterResponseDto($newUserToken);
        } catch (ValidationException $e) {
            return $e->getResponse();
        } catch (Exception $e) {
            $responseDto = new InternalErrorResponseDto();
        }

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }
}

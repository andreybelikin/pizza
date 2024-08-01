<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Response\InternalErrorResponseDto;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;
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
        } catch (ValidationException $exception) {
            $response = $exception->getResponse();
        } catch (InvalidCredentialsException $exception) {
            $response = response()->json(['message' => $exception->getMessage()], $exception->getCode());
        } catch (Exception) {
            $responseDto = new InternalErrorResponseDto();

            $response = response()->json($responseDto->toArray(), $responseDto::STATUS);
        }

        return $response;
    }

    public function register(RegisterRequest $request): Response
    {
        $newUserToken = $this->authService->saveNewUser($request);

        return response()->header('Authorization', $newUserToken);
    }
}

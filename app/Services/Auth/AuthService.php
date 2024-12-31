<?php

namespace App\Services\Auth;

use App\Dto\Request\LoginData;
use App\Dto\Request\RegisterUserData;
use App\Dto\Request\TokensData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\TokensRequest;
use App\Http\Resources\UserResource;
use App\Services\DBTransactionService;
use App\Services\Resource\UserDataService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthService
{
    public function __construct(
        private UserDataService $userDataService,
        private DBTransactionService $dbTransactionService
    ) {}

    public function registerUser(RegisterRequest $request): JsonResource
    {
        $registerData = RegisterUserData::fromRequest($request);
        $user = $this->dbTransactionService->execute(fn () => $this->userDataService->createUser($registerData));

        return new UserResource($user);
    }

    public function authenticateUser(LoginRequest $request): array
    {
        $loginData = LoginData::fromRequest($request)->toArray();
        $accessToken = auth()->attempt($loginData);

        if (!$accessToken) {
            throw new InvalidCredentialsException();
        }

        $refreshToken = $this->generateRefreshToken();

        return [$accessToken, $refreshToken];
    }

    public function refreshToken(TokensRequest $request): array
    {
        $this->logoutUser($request);
        $authenticatedUser = auth()->user();

        $accessToken = $this->loginUser($authenticatedUser);
        $refreshToken = $this->generateRefreshToken();

        return [$accessToken, $refreshToken];
    }

    public function logoutUser(Request $request): void
    {
        auth()->setToken($request->bearerToken());
        auth()->invalidate();
    }

    private function generateRefreshToken(): string
    {
        return (auth()->claims([
                'typ' => 'refresh',
                'exp' => now()->addMinutes(config('jwt.refresh_ttl'))
            ])
        )->tokenById(auth()->user()->getJWTIdentifier());
    }

    private function loginUser(Authenticatable $user): string
    {
        return auth()->login($user);
    }
}

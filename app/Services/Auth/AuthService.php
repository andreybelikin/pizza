<?php

namespace App\Services\Auth;

use App\Dto\Request\LoginData;
use App\Dto\Request\TokensData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\TokensRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthService
{
    public function registerUser(RegisterRequest $request): void
    {
        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
            'default_address' => $request->input('default_address'),
            'is_admin' => 0,
        ]);
        $user->save();
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

    public function logoutUser(TokensRequest $request): void
    {
        $tokens = TokensData::fromRequest($request)->toArray();

        array_walk($tokens, function ($token) {
            auth()->setToken($token);
            auth()->invalidate();
        });
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

<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

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
        $credentials = $request->only('email', 'password');
        $accessToken = auth()->attempt($credentials);

        if (!$accessToken) {
            throw new InvalidCredentialsException();
        }

        $refreshToken = $this->generateRefreshToken();

        return [$accessToken, $refreshToken];
    }

    public function refreshToken(Request $request): array
    {
        auth()->setToken($request->header('x-refresh-token'));
        $authenticatedUser = auth()->user();
        auth()->invalidate();

        $accessToken = $this->loginUser($authenticatedUser);
        $refreshToken = $this->generateRefreshToken();

        return [$accessToken, $refreshToken];
    }

    public function logoutUser(Request $request): void
    {
        $tokens = array_filter([
            $request->bearerToken(),
            $request->header('x-refresh-token')
        ]);

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

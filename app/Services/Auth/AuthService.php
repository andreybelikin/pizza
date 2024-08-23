<?php

namespace App\Services\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthService
{
    public function saveNewUser(RegisterRequest $request): void
    {
        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
            'default_address' => $request->input('default_address'),
            'is_admin' => false,
        ]);
        $user->save();
    }

    public function authenticateUser(AuthenticateRequest $request): array
    {
        $credentials = $request->only('email', 'password');
        $accessToken = auth()->attempt($credentials);
        $refreshToken = $this->generateRefreshToken();

        if (!$accessToken) {
            throw new InvalidCredentialsException(
                'Invalid email or password',
                Response::HTTP_BAD_REQUEST
            );
        }

        return [$accessToken, $refreshToken];
    }

    public function refreshToken(): array
    {
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
            auth()->logout();
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

<?php

namespace Tests\Traits;

use App\Models\User;

trait AuthTrait
{
    public static function generateAccessToken(string $email, string $password): void
    {
        auth()->attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function getRefreshToken()
    {
        return (
            auth()
            ->claims(
            [
                'typ' => 'refresh',
                'exp' => now()->addMinutes(config('jwt.refresh_ttl'))
            ])
        )->tokenById(
            auth()
            ->user()
            ->getJWTIdentifier()
        );
    }

    public function getUserAccessToken(User $user): string
    {
        return auth()->fromUser($user);
    }

    public function getInvalidToken(): string
    {
        return 'eyJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEifQ.ZAU547bnCcGrvSZiaDeYpbQg6rUopOe3HMJ01l2a2NQ';
    }
}

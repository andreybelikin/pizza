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

    public function generateRefreshToken()
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

    public function getAccessTokenFromUser(User $user): string
    {
        return auth()->fromUser($user);
    }
}

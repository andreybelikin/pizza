<?php

namespace Tests\TestData;

class Tokens
{
    public static function generateAccessToken(string $email, string $password): void
    {
        auth()->attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }
    public static function generateRefreshToken()
    {
        return (auth()->claims([
                'typ' => 'refresh',
                'exp' => now()->addMinutes(config('jwt.refresh_ttl'))
            ])
        )->tokenById(auth()->user()->getJWTIdentifier());
    }
}

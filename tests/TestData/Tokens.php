<?php

namespace Tests\TestData;

class Tokens
{
    public static function generateAccessToken(string $email, string $password): string
    {
        return auth()->attempt([
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

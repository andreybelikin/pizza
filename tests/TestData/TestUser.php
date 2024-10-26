<?php

namespace Tests\TestData;

use App\Models\User;

class TestUser
{
    public static string $plainPassword;

    public static function createAdminAuthorizedUser(): void
    {
        $password = fake()->password();
        $user = User::factory()
            ->setPassword($password)
            ->create(['is_admin' => true]);
        Tokens::generateAccessToken($user->email, $password);
    }

    public static function createAuthorizedUser(): void
    {
        $user = User::first();
        Tokens::generateAccessToken($user->email, $user->password);
    }
    public static function createUserWithCredentials(array $credentials): void
    {
        $credentials['password'] = bcrypt($credentials['password']);
        User::factory()->create($credentials);
    }

    public static function createAnotherUser(): User
    {
        return User::factory()->create();
    }
}

<?php

namespace Tests\TestData;

use App\Models\User;

class TestUser
{
    public static string $plainPassword;

    private static function createPassword(): string
    {
        self::$plainPassword = fake()->password();

        return self::$plainPassword;
    }

    public static function createUserForToken(): User
    {
        $password = self::createPassword();

        return User::factory()->setPassword($password)->create();
    }

    public static function createUserWithCredentials(array $credentials): void
    {
        $credentials['password'] = bcrypt($credentials['password']);
        User::factory()->create($credentials);
    }

    public static function createManyUsers(): void
    {
        User::factory(3)->create();
    }
}

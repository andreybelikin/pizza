<?php

namespace Tests\TestData;

use App\Models\User;

class TestUser
{
    public static string $plainPassword;

    private static function createPassword(): void
    {
        self::$plainPassword = fake()->password();
    }

    public static function createUserForToken(): User
    {
        self::createPassword();

        return User::factory()->setPassword(self::$plainPassword)->create();
    }

    public static function createUserWithCredentials(array $credentials): void
    {
        User::factory()->create($credentials);
    }

    public static function createManyUsers(): void
    {
        User::factory(3)->create();
    }
}

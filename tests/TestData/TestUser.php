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

    public static function createUserWithCredentials(): void
    {
        $credentials = [
            'email' => 'test2233@email.com',
            'password' => bcrypt('keK48!>O04780'),
        ];

        User::factory()->create($credentials);
    }

    public static function createManyUsers(): void
    {
        User::factory(3)->create();
    }
}

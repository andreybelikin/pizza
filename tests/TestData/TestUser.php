<?php

namespace Tests\TestData;

use App\Models\User;

class TestUser
{
    public static string $plainPassword;

    public static function createAdminAuthorizedUser(): void
    {
        $password = fake()->password();
        $user = User::factory()->setPassword($password)->create(['is_admin' => true]);
        Tokens::generateAccessToken($user->email, $password);
    }

    public static function createUserWithCredentials(): void
    {
        $credentials = [
            'email' => 'test2233@email.com',
            'password' => bcrypt('keK48!>O04780'),
        ];

        User::factory()->create($credentials);
    }

    public static function createAnotherUser(): User
    {
        return User::factory()->create();
    }
}

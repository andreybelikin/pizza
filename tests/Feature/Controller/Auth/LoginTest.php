<?php

namespace Tests\Feature\Controller\Auth;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testLoginShouldSuccess(string $route): void
    {
        $credentials = [
            'email' => 'test2233@email.com',
            'password' => 'keK48!>O04780',
        ];
        $user = $this->createUserWithCredentials($credentials);

        $response = $this->postJson($route, $credentials);
        $accessToken = $response->decodeResponseJson()['accessToken'];
        $refreshToken = $response->decodeResponseJson()['refreshToken'];

        $response->assertOk();
        $tokenUser = auth()->setToken($accessToken)->authenticate();
        static::assertSame($user->id, $tokenUser->id);
        $tokenUser = auth()->setToken($refreshToken)->authenticate();
        static::assertSame($user->id, $tokenUser->id);
    }

    #[DataProvider('contextDataProvider')]
    public function testLoginWithInvalidCredentialsShouldFail(string $route): void
    {
        $credentials = [
            'email' => 2233,
            'password' => 'keK48!>O04780',
        ];
        $response = $this->postJson($route, $credentials);

        $response->assertUnprocessable();
    }

    #[DataProvider('contextDataProvider')]
    public function testLoginWithMismatchedCredentialsShouldFail(string $route): void
    {
        $initialCredentials = [
            'email' => 'test2233@email.com',
            'password' => 'keK48!>O04780',
        ];
        $mismatchedCredentials = [
            'email' => 'test2233@email.RU',
            'password' => 'keK48!>O04780',
        ];
        $user = $this->createUserWithCredentials($initialCredentials);

        $response = $this->postJson($route, $mismatchedCredentials);
        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'route' => route('auth.login'),
            ],
            'admin' => [
                'route' => route('admin.auth.login'),
            ]
        ];
    }
}

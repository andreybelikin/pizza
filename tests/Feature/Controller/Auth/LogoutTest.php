<?php

namespace Tests\Feature\Controller\Auth;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testLogoutShouldSuccess(string $route): void
    {
        $user = $this->getUser();
        $accessToken = $this->getUserAccessToken($user);
        $refreshToken = $this->getRefreshToken($user->id);
        $logoutResponse = $this->postJson(
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $logoutResponse->assertOk();

        $ensureLogoutResponse = $this->postJson(
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $ensureLogoutResponse->assertUnauthorized();
    }

    #[DataProvider('contextDataProvider')]
    public function testLogoutWithDeletedUserShouldFail(string $route): void
    {
        $user = $this->getUser();
        $accessToken = $this->getUserAccessToken($user);
        $refreshToken = $this->getRefreshToken($user->id);

        $user->delete();

        $logoutResponse = $this->postJson(
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $logoutResponse->assertUnauthorized();
    }

    #[DataProvider('contextDataProvider')]
    public function testLogoutWithInvalidTokenShouldFail(string $route): void
    {
        $user = $this->getUser();
        $accessToken = $this->getInvalidToken();
        $refreshToken = $this->getRefreshToken($user->id);

        $logoutResponse = $this->postJson(
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $logoutResponse->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'route' => route('auth.logout'),
            ],
            'admin' => [
                'route' => route('admin.auth.logout'),
            ]
        ];
    }
}

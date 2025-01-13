<?php

namespace Tests\Feature\Controller\Auth;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testLogoutShouldSuccess(\Closure $user, string $route): void
    {
        $user = $user($this);
        $accessToken = $this->getUserAccessToken($user);
        $refreshToken = $this->getRefreshToken($user->id);

        $logoutResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $logoutResponse->assertOk();

        $ensureLogoutResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $ensureLogoutResponse->assertUnauthorized();
    }

    #[DataProvider('contextDataProvider')]
    public function testLogoutWithDeletedUserShouldFail(\Closure $user, string $route): void
    {
        $user = $user($this);
        $accessToken = $this->getUserAccessToken($user);
        $refreshToken = $this->getRefreshToken($user->id);

        $user->delete();

        $logoutResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $accessToken,
                'x-refresh-token' => $refreshToken,
            ]
        );
        $logoutResponse->assertUnauthorized();
    }

    #[DataProvider('contextDataProvider')]
    public function testLogoutWithInvalidTokenShouldFail(\Closure $user, string $route): void
    {
        $user = $user($this);
        $accessToken = $this->getInvalidToken();
        $refreshToken = $this->getRefreshToken($user->id);

        $logoutResponse = $this->postJson(
            route($route),
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
                'user' => fn($self) => $self->getUser(),
                'route' => 'auth.logout',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.auth.logout',
            ]
        ];
    }
}

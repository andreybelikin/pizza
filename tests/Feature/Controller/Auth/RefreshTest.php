<?php

namespace Tests\Feature\Controller\Auth;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testRefreshShouldSuccess(\Closure $user, string $route)
    {
        $user = $user($this);
        $oldAccessToken = $this->getUserAccessToken($user);
        $oldRefreshToken = $this->getRefreshToken($user->id);

        $refreshResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $oldAccessToken,
                'x-refresh-token' => $oldRefreshToken,
            ]
        );
        $refreshResponse->assertOk();
        $newTokens = $refreshResponse->decodeResponseJson();
        static::assertEquals(2, count(DB::table('token_blacklist')->get()));

        $ensureUnauthorizedResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $oldAccessToken,
                'x-refresh-token' => $oldRefreshToken,
            ]
        );
        $ensureUnauthorizedResponse->assertUnauthorized();

        $ensureOkResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $newTokens['accessToken'],
                'x-refresh-token' => $newTokens['refreshToken'],
            ]
        );
        $ensureOkResponse->assertOk();
    }

    #[DataProvider('contextDataProvider')]
    public function testRefreshWithEmptyTokenShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);
        $refreshResponse = $this->postJson(
            route($route),
            [],
            [
                'authorization' => 'Bearer ' . $this->getUserAccessToken($user),
                'x-refresh-token' => '',
            ]
        );
        $refreshResponse->assertUnauthorized();
    }
    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn($self) => $self->getUser(),
                'route' => 'auth.refresh',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.auth.refresh',
            ]
        ];
    }
}

<?php

namespace Tests\Feature\Controller\Auth;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testRefreshShouldSuccess(string $route)
    {
        $user = $this->getUser();
        $oldAccessToken = $this->getUserAccessToken($user);
        $oldRefreshToken = $this->getRefreshToken($user->id);

        $refreshResponse = $this->postJson(
            $route,
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
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $oldAccessToken,
                'x-refresh-token' => $oldRefreshToken,
            ]
        );
        $ensureUnauthorizedResponse->assertUnauthorized();

        $ensureOkResponse = $this->postJson(
            $route,
            [],
            [
                'authorization' => 'Bearer ' . $newTokens['accessToken'],
                'x-refresh-token' => $newTokens['refreshToken'],
            ]
        );
        $ensureOkResponse->assertOk();
    }

    #[DataProvider('contextDataProvider')]
    public function testRefreshWithEmptyTokenShouldFail(string $route)
    {
        $user = $this->getUser();
        $refreshResponse = $this->postJson(
            $route,
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
                'route' => route('auth.refresh'),
            ],
            'admin' => [
                'route' => route('admin.auth.refresh'),
            ]
        ];
    }
}

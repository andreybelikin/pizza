<?php

namespace Tests\Feature\Controller\User;

use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\UserTrait;

class UserDeleteTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testDeleteUserShouldSuccess(\Closure $user, Closure $route): void
    {
        $user = $user($this);

        $response = $this->deleteJson(
            $route($user->getKey()),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );
        $response->assertOk();
        static::assertModelMissing($user);
    }

    public function testDeleteAnotherUserByAdminShouldSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->deleteJson(
            route('admin.users.destroy', ['id' => $anotherUser->getKey()]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        static::assertModelMissing($anotherUser);
    }

    public function testDeleteAnotherUserShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->deleteJson(
            route('users.destroy', ['id' => $anotherUser->getKey()]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    #[DataProvider('contextDataProvider')]
    public function testDeleteNonExistentUserShouldFail(\Closure $user, \Closure $route): void
    {
        $user = $user($this);

        $response = $this->deleteJson(
            $route(999999),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }

    #[DataProvider('contextDataProvider')]
    public function testDeleteUserWithInvalidTokenShouldFail(\Closure $user, \Closure $route): void
    {
        $user = $user($this);
        $response = $this->deleteJson(
            $route($user->getKey()),
            [],
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => fn (int $userId) => route('users.destroy', ['userId' => $userId]),
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => fn (int $userId) => route('admin.users.destroy', ['userId' => $userId]),
            ]
        ];
    }
}

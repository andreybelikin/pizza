<?php

namespace Tests\Feature\Controller\User;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserGetTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testGetUserShouldSuccess(\Closure $user, string $route): void
    {
        $user = $user($this);

        $response = $this->getJson(
            route($route, ['user' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson([
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'phone' => $user->phone,
            'default_address' => $user->default_address,
        ]);
    }

    public function testGetAnotherUserByAdminShouldSuccess()
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->getJson(
            route('admin.users.show', ['user' => $anotherUser->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson([
            'name' => $anotherUser->name,
            'surname' => $anotherUser->surname,
            'email' => $anotherUser->email,
            'phone' => $anotherUser->phone,
            'default_address' => $anotherUser->default_address,
        ]);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetNonExistentUserShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);

        $response = $this->getJson(
            route($route, ['user' => 9999]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }

    public function testGetAnotherUserShouldFail()
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->getJson(
            route('users.show', ['user' => $anotherUser->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    #[DataProvider('contextDataProvider')]
    public function testUserWithInvalidTokenShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);

        $response = $this->getJson(
            route($route, ['user' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'users.show',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.users.show',
            ]
        ];
    }
}

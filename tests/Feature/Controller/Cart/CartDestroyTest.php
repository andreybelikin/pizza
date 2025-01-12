<?php

namespace Tests\Feature\Controller\Cart;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CartDestroyTest extends TestCase
{
    public function testDeleteCartByOwnerSuccess(): void
    {
        $user = $this->createUser();
        $this->createCartProducts($user);

        $response = $this->deleteJson(
            route('users.cart.destroy', ['userId' => $user->getKey()]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        static::assertTrue($user->products()->get()->isEmpty());
    }

    public function testDeleteAnotherUserCartByAdminShouldSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);

        $response = $this->deleteJson(
            route('admin.users.cart.destroy', ['userId' => $anotherUser->getKey()]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        static::assertTrue($anotherUser->products()->get()->isEmpty());
    }

    #[DataProvider('contextDataProvider')]
    public function testDeleteCartWithInvalidTokenShouldFail(\Closure $user, \Closure $route): void
    {
        $user = $user($this);
        $this->createCartProducts($user);

        $response = $this->deleteJson(
            $route($user->getKey()),
            [],
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testDeleteAnotherUserCartShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);

        $response = $this->deleteJson(
            route('users.cart.destroy', ['userId' => $user->getKey()]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => fn (int $userId) => route('users.cart.destroy', ['userId' => $userId])
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => fn (int $userId) => route('admin.users.cart.destroy', ['userId' => $userId])
            ]
        ];
    }
}

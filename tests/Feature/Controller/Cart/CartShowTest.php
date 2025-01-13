<?php

namespace Tests\Feature\Controller\Cart;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CartShowTest extends TestCase
{
    public function testGetCartByOwnerSuccess(): void
    {
        $user = $this->getUser();
        $expectedCart = $this->getCart($user);
        $response = $this->getJson(
            route('users.cart.show', ['userId' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedCart);
    }

    public function testGetAnotherUserCartByAdminShouldSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();
        $expectedCart = $this->getCart($anotherUser);

        $response = $this->getJson(
            route('admin.users.cart.show', ['userId' => $anotherUser->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedCart);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetCartWithInvalidTokenShouldFail(\Closure $user, string $route): void
    {
        $user = $user($this);

        $response = $this->getJson(
            route($route, ['userId' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testGetAnotherUserCartShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->getJson(
            route('users.cart.show', ['userId' => $anotherUser->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'users.cart.show',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.users.cart.show',
            ]
        ];
    }
}

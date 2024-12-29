<?php

namespace Tests\Feature\Controller\Cart;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CartGetTest extends TestCase
{
    private const USER_CONTROLLER_ROUTE = '/api/users/{userId}/carts';
    private const ADMIN_CONTROLLER_ROUTE = '/api/admin/users/{userId}/carts';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    #[DataProvider('contextDataProvider')]
    public function testGetCartByOwnerSuccess(\Closure $user, string $route): void
    {
        $user = $user($this);
        $expectedCart = $this->getCart($user);
        $response = $this->getJson(
            str_replace('{userId}', $user->getKey(), $route),
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
            str_replace('{userId}', $user->getKey(), $route),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testGetAnotherUserCartShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();

        $response = $this->getJson(
            str_replace('{userId}', $anotherUser->getKey(), self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => self::USER_CONTROLLER_ROUTE,
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => self::ADMIN_CONTROLLER_ROUTE,
            ]
        ];
    }
}

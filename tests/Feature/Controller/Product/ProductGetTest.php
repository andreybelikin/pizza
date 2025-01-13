<?php

namespace Tests\Feature\Controller\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductGetTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testGetProductShouldSuccess(\Closure $user, string $route): void
    {
        $expectedProduct = $this->createProduct();

        $response = $this->getJson(
            route($route, ['product' => $expectedProduct->id]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertOk();
        $response->assertJson($expectedProduct->only([
            'title',
            'description',
            'type',
            'price',
        ]));
    }

    #[DataProvider('contextDataProvider')]
    public function testGetNonExistentProductShouldFail(\Closure $user, string $route): void
    {
        $response = $this->getJson(
            route($route, ['product' => 99999]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertNotFound();
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductWithInvalidTokenShouldFail(\Closure $user, string $route): void
    {
        $expectedProduct = $this->createProduct();

        $response = $this->getJson(
            route($route, ['product' => $expectedProduct->id]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'products.show',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.products.show',
            ]
        ];
    }
}

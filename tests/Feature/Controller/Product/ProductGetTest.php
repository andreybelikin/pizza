<?php

namespace Tests\Feature\Controller\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductGetTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testGetProductShouldSuccess(\Closure $user, \Closure $route): void
    {
        $expectedProduct = $this->createProduct();

        $response = $this->getJson(
            $route($expectedProduct->id),
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
    public function testGetNonExistentProductShouldFail(\Closure $user, \Closure $route): void
    {
        $response = $this->getJson(
            $route(9999999),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertNotFound();
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductWithInvalidTokenShouldFail(\Closure $user, \Closure $route): void
    {
        $expectedProduct = $this->createProduct();

        $response = $this->getJson(
            $route($expectedProduct->id),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => fn (int $productId) => route('products.show', ['id' => $productId])
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => fn (int $productId) => route('admin.products.show', ['id' => $productId])
            ]
        ];
    }
}

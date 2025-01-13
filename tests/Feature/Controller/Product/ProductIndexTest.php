<?php

namespace Tests\Feature\Controller\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithoutFiltersShouldSuccess(\Closure $user, string $route): void
    {
        $expectedResult['data'] = $this->getProducts();
        $expectedResult['pagination'] = [
            'currentPage' => 1,
            'perPage' => 15,
            'total' => count($expectedResult['data']),
        ];

        $response = $this->getJson(
            route($route),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithFiltersShouldSuccess(\Closure $user, string $route): void
    {
        $filters = [
            'title' => 'testProduct',
            'description' => 'testDescription',
            'type' => 'pizza',
            'minPrice' => '30000000',
            'maxPrice' => '70000000',
        ];
        $productsForFilters = [
            [
                'title' => 'testProduct',
                'description' => 'testDescription',
                'type' => 'pizza',
                'price' => '40000000'
            ],
            [
                'title' => 'testProduct',
                'description' => 'testDescription',
                'type' => 'pizza',
                'price' => '50000000'
            ],
            [
                'title' => 'testProduct',
                'description' => 'testDescription',
                'type' => 'pizza',
                'price' => '60000000'
            ],
        ];
        $expectedResult['data'] = $this->createProducts($productsForFilters);
        $expectedResult['pagination'] = [
            'currentPage' => 1,
            'perPage' => 15,
            'total' => count($expectedResult['data']),
        ];

        $response = $this->getJson(
            route($route, $filters),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithNotValidFiltersShouldFail(\Closure $user, string $route): void
    {
        $response = $this->getJson(
            route($route, ['type' => 'fish']),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertUnprocessable();
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithInvalidTokenShouldFail(\Closure $user, string $route): void
    {
        $response = $this->getJson(
            route($route, ['type' => 'fish']),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'products.index',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.products.index',
            ]
        ];
    }
}

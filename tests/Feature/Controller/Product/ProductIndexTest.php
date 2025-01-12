<?php

namespace Tests\Feature\Controller\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithoutFiltersShouldSuccess(\Closure $user, \Closure $route): void
    {
        $expectedResult['data'] = $this->getProducts();
        $expectedResult['pagination'] = [
            'currentPage' => 1,
            'perPage' => 15,
            'total' => count($expectedResult['data']),
        ];

        $response = $this->getJson(
            $route(),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithFiltersShouldSuccess(\Closure $user, \Closure $route): void
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
            $route($filters),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithNotValidFiltersShouldFail(\Closure $user, \Closure $route): void
    {
        $response = $this->getJson(
            $route(['type' => 'fish']),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user($this))]
        );

        $response->assertUnprocessable();
    }

    #[DataProvider('contextDataProvider')]
    public function testGetProductsWithInvalidTokenShouldFail(\Closure $user, \Closure $route): void
    {
        $response = $this->getJson(
            $route(['type' => 'fish']),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => fn (array $filters = []) => route('products.index', $filters)
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => fn (array $filters = []) => route('admin.products.index', $filters)
            ]
        ];
    }
}

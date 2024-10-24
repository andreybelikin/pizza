<?php

namespace Tests\Feature\Controller\Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestData\TestUser;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    private const CONTROLLER_ROUTE = '/api/product';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        TestUser::createAdminAuthorizedUser();
    }

    public function testProductUpdateSuccess(): void
    {
        $product = $this->createProduct();

        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . '/' . $product->getKey(),
            ['title' => 'Пицца века'],
            ['authorization' => 'Bearer ' . auth()->getToken()]
        );

        $response->assertStatus(Response::HTTP_OK);
        $decodedResponse = $response->decodeResponseJson();

        static::assertSame('Пицца века', $decodedResponse['title']);
    }

    public function testProductUpdateWithInvalidTokenShouldFail(): void
    {
        $invalidToken = 'eyJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEifQ.ZAU547bnCcGrvSZiaDeYpbQg6rUopOe3HMJ01l2a2NQ';
        auth()->setToken($invalidToken);

        $product = $this->createProduct();

        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . '/' . $product->getKey(),
            ['title' => 'Пицца века'],
            ['authorization' => 'Bearer ' . auth()->getToken()]
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $decodedResponse = $response->decodeResponseJson();

        static::assertArrayHasKey('message', $decodedResponse);
        static::assertSame('Token Signature could not be verified.', $decodedResponse['message']);
    }

    public function testProductUpdateWithNonExistentProductShouldFail(): void
    {
        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . '/' . rand(1000, 2000),
            ['title' => 'Пицца века'],
            ['authorization' => 'Bearer ' . auth()->getToken()]
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $decodedResponse = $response->decodeResponseJson();

        static::assertArrayHasKey('message', $decodedResponse);
        static::assertSame('Resource is not exist', $decodedResponse['message']);
    }

    public function testProductUpdateWithEmptyResponseBodyShouldFail(): void
    {
        $product = $this->createProduct();

        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . '/' . $product->getKey(),
            [],
            ['authorization' => 'Bearer ' . auth()->getToken()]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $decodedResponse = $response->decodeResponseJson();

        static::assertArrayHasKey('message', $decodedResponse);
        static::assertSame('The given data failed to pass validation', $decodedResponse['message']);
    }


    private function createProduct(): Product
    {
        return Product::factory()->createOne();
    }
}

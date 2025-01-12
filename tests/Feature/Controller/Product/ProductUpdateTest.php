<?php

namespace Tests\Feature\Controller\Product;

use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    public function testProductUpdateShouldSuccess(): void
    {
        $adminUser = $this->getAdminUser();
        $product = $this->getRandomProduct();
        $productPropsToUpdate = [
            'title' => 'test title',
            'description' => 'test description',
            'type' => 'drink',
            'price' => 100,
        ];

        $response = $this->putJson(
            route('admin.products.update', ['id' => $product->id]),
            $productPropsToUpdate,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($adminUser)]
        );
        $expectedProduct = $productPropsToUpdate;
        $expectedProduct['id'] = $product->id;

        $response->assertOk();
        $response->assertJson($expectedProduct);
    }

    public function testProductUpdateWithInvalidTokenShouldFail(): void
    {
        $product = $this->getRandomProduct();

        $response = $this->putJson(
            route('admin.products.update', ['id' => $product->id]),
            ['title' => 'Пицца века'],
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testProductUpdateWithNonExistentProductShouldFail(): void
    {
        $adminUser = $this->getAdminUser();
        $response = $this->putJson(
            route('admin.products.update', ['id' => 99999]),
            ['title' => 'Пицца века'],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($adminUser)]
        );

        $response->assertNotFound();
    }

    public function testProductUpdateWithWithNotValidDataShouldFail(): void
    {
        $adminUser = $this->getAdminUser();
        $product = $this->getRandomProduct();

        $response = $this->putJson(
            route('admin.products.update', ['id' => $product->id]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($adminUser)]
        );

        $response->assertUnprocessable();
    }
}

<?php

namespace Tests\Feature\Controller\Product;

use Tests\TestCase;

class ProductDeleteTest extends TestCase
{
    public function testDeleteProductShouldSuccess(): void
    {
        $adminUser = $this->getAdminUser();
        $product = $this->getRandomProduct();

        $response = $this->deleteJson(
            route('admin.products.destroy', ['id' => $product->id]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($adminUser)]
        );

        $response->assertOk();
        static::assertNull($this->getNullProduct($product->id));
    }

    public function testProductDeleteWithInvalidTokenShouldFail(): void
    {
        $product = $this->getRandomProduct();

        $response = $this->deleteJson(
            route('admin.products.destroy', ['id' => $product->id]),
            [],
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testProductDeleteWithNonExistentProductShouldFail(): void
    {
        $adminUser = $this->getAdminUser();
        $response = $this->deleteJson(
            route('admin.products.destroy', ['id' => rand(1000, 2000)]),
            [],
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($adminUser)]
        );

        $response->assertNotFound();
    }
}

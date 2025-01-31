<?php

namespace Tests\Feature\Controller\Order;

use App\Enums\OrderStatus;
use App\Models\Product;
use Tests\TestCase;

class OrderAddAdminTest extends TestCase
{
    public function testAddOrderByAdminSuccess(): void
    {
        $admin = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();
        $products = $this->getProductsForNewOrder();
        $orderData = [
            'userId' => $anotherUser->id,
            'status' => OrderStatus::Created,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => $products->map(fn($product) => [
                'id' => $product->id,
                'quantity' => 1,
            ])->toArray(),
        ];
        $response = $this->postJson(
            route('admin.users.orders.store', ['userId' => $anotherUser->getKey()]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($admin)]
        );
        $expectedProducts = $products->map(
            function (Product $product) {
                return [
                    'title' => $product->title,
                    'quantity' => 1,
                    'price' => $product->price,
                    'totalPrice' => $product->price,
                ];
            }
        );
        $productsTotalSum = $expectedProducts->sum('totalPrice');
        $expectedResult = [
            'order' => [
                'name' => 'test name',
                'phone' => '89996668877',
                'address' => 'test address',
                'status' => OrderStatus::Created,
                'total' => $productsTotalSum,
            ],
            'orderProducts' => $expectedProducts->toArray(),
        ];

        $response->assertCreated();
        $response->assertJsonFragment($expectedResult['order']);
        $response->assertJsonFragment($expectedResult['orderProducts'][0]);
        $response->assertJsonFragment($expectedResult['orderProducts'][1]);
        $this->assertEquals('test address', $this->getUserAddress($anotherUser));
    }

    public function testOrderAddByAdminWithInvalidTokenShouldFail(): void
    {
        $anotherUser = $this->getAnotherUser();
        $products = $this->getProductsForNewOrder();
        $orderData = [
            'status' => OrderStatus::Created,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => $products->map(fn($product) => [
                'id' => $product->id,
                'quantity' => 1,
            ])->toArray(),
        ];
        $response = $this->postJson(
            route('admin.users.orders.store', ['userId' => $anotherUser->getKey()]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testOrderAddByAdminWithoutProductsShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();
        $orderData = [
            'status' => OrderStatus::Created,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => [],
        ];
        $response = $this->postJson(
            route('admin.users.orders.store', ['userId' => $anotherUser->getKey()]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($admin)]
        );

        $response->assertUnprocessable();
    }
}

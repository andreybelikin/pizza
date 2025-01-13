<?php

namespace Tests\Feature\Controller\Order;

use App\Enums\OrderStatus;
use App\Models\OrderProduct;
use Tests\TestCase;

class OrderUpdateAdminTest extends TestCase
{
    public function testAddOrderUpdateByAdminSuccess(): void
    {
        $admin = $this->getAdminUser();
        $order = $this->getUserOrder($this->getAnotherUser());
        $orderProductsData = $order->orderProducts->map(fn (OrderProduct $product) => [
            'id' => $product->id,
            'title' => 'test title',
            'description' => 'test description',
            'type' =>  'drink',
            'quantity' => 3,
            'price' => 3200,
        ]);
        $orderData = [
            'status' => OrderStatus::Delivered,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => $orderProductsData,
        ];
        $response = $this->putJson(
            route('admin.orders.update', ['order' => $order->id]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($admin)]
        );
        $expectedResult = $orderData;
        $expectedResult['orderProducts'] = $orderProductsData->map(function ($product) {
            return [
                'id' => $product['id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'title' => $product['title'],
                'totalPrice' => $product['price'] * $product['quantity'],
            ];
        });
        $expectedResult['total'] = $orderProductsData->sum(fn ($product) => $product['price'] * $product['quantity']);

        $response->assertOk();
        $response->assertJsonFragment($expectedResult);
    }

    public function testOrderUpdateByAdminWithInvalidTokenShouldFail(): void
    {
        $order = $this->getUserOrder($this->getAnotherUser());
        $orderProductsData = $order->orderProducts->map(fn (OrderProduct $product) => [
            'id' => $product->id,
            'title' => 'test title',
            'description' => 'test description',
            'type' =>  'drink',
            'quantity' => 3,
            'price' => 3200,
        ]);
        $orderData = [
            'status' => OrderStatus::Delivered,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => $orderProductsData,
        ];
        $response = $this->putJson(
            route('admin.orders.update', ['order' => $order->id]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testOrderUpdateByAdminWithNonExistentOrderIdShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $order = $this->getUserOrder(
            $this->getAnotherUser()
        );
        $orderProductsData = $order->orderProducts->map(fn (OrderProduct $product) => [
            'id' => $product->id,
            'title' => 'test title',
            'description' => 'test description',
            'type' =>  'drink',
            'quantity' => 3,
            'price' => 3200,
        ]);
        $orderData = [
            'status' => OrderStatus::Delivered,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
            'orderProducts' => $orderProductsData,
        ];
        $response = $this->putJson(
            route('admin.orders.update', ['order' => 999999]),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($admin)]
        );

        $response->assertNotFound();
    }
}

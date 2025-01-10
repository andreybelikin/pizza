<?php

namespace Tests\Feature\Controller\Order;

use App\Enums\OrderStatus;
use App\Models\Product;
use Tests\TestCase;

class OrderAddTest extends TestCase
{
    private const USER_CONTROLLER_ROUTE = 'api/users/{userId}/orders';

    public function testAddOrderSuccess(): void
    {
        $user = $this->createUser();
        $products = $this->getProductsForNewOrder();
        $this->createUserCartProducts($user, $products->toArray());
        $orderData = [
            'userId' => $user->id,
            'status' => OrderStatus::CREATED,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
        ];
        $response = $this->postJson(
            str_replace('{userId}', $user->getKey(), self::USER_CONTROLLER_ROUTE),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
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
                'status' => OrderStatus::CREATED,
                'total' => $productsTotalSum,
            ],
            'orderProducts' => $expectedProducts->toArray(),
        ];

        $response->assertCreated();
        $response->assertJsonFragment($expectedResult['order']);
        $response->assertJsonFragment($expectedResult['orderProducts'][0]);
        $response->assertJsonFragment($expectedResult['orderProducts'][1]);
        $this->assertEquals('test address', $this->getUserAddress($user));
    }

    public function testOrderAddWithInvalidTokenShouldFail(): void
    {
        $user = $this->getUser();
        $products = $this->getProductsForNewOrder();
        $this->createUserCartProducts($user, $products->toArray());
        $orderData = [
            'status' => OrderStatus::CREATED,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
        ];
        $response = $this->postJson(
            str_replace('{userId}', $user->getKey(), self::USER_CONTROLLER_ROUTE),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testOrderAddForAnotherUserShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();
        $products = $this->getProductsForNewOrder();
        $this->createUserCartProducts($user, $products->toArray());
        $orderData = [
            'status' => OrderStatus::CREATED,
            'phone' => '89996668877',
            'address' => 'test address',
            'name' => 'test name',
        ];
        $response = $this->postJson(
            str_replace('{userId}', $anotherUser->getKey(), self::USER_CONTROLLER_ROUTE),
            $orderData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }
}

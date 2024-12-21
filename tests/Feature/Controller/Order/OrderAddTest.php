<?php

namespace Feature\Controller\Order;

use App\Enums\OrderStatus;
use App\Models\Product;
use Tests\TestCase;

class OrderAddTest extends TestCase
{
    private const CONTROLLER_ROUTE = 'api/users/{userId}/orders';

    public function testAddOrderSuccess(): void
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
            str_replace('{userId}', $user->getKey(), self::CONTROLLER_ROUTE),
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
            'name' => 'test name',
            'phone' => '89996668877',
            'address' => 'test address',
            'status' => OrderStatus::CREATED,
            'orderProducts' => $expectedProducts->toArray(),
            'total' => $productsTotalSum,
        ];

        $response->assertOk();
        $response->assertJsonFragment($expectedResult);
    }
}

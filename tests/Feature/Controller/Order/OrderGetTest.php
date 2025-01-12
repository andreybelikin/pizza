<?php

namespace Tests\Feature\Controller\Order;

use Tests\TestCase;

class OrderGetTest extends TestCase
{
    public function testGetOrderByOwnerSuccess(): void
    {
        $user = $this->getUser();
        $expectedResult = $this->createOrder($user);
        $response = $this->getJson(
            route('users.orders.show', ['orderId' => $expectedResult['data']['id']]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['data']);
    }

    public function testGetOrderWithNonExistentOrderShouldFail(): void
    {
        $user = $this->getUser();
        $response = $this->getJson(
            route('users.orders.show', ['orderId' => 99999]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }

    public function testGetOrderByAnotherUserShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();
        $orderId = $this->getUserOrder($anotherUser)->id;
        $response = $this->getJson(
            route('users.orders.show', ['orderId' => $orderId]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    public function testGetOrderByOwnerWithInvalidTokenShouldFail(): void
    {
        $user = $this->getUser();
        $orderId = $this->getUserOrder($user)->id;
        $response = $this->getJson(
            route('users.orders.show', ['orderId' => $orderId]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }
}

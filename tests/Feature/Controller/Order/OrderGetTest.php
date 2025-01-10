<?php

namespace Tests\Feature\Controller\Order;

use Tests\TestCase;

class OrderGetTest extends TestCase
{
    private const USER_CONTROLLER_ROUTE = 'api/orders/{orderId}';

    public function testGetOrderByOwnerSuccess(): void
    {
        $user = $this->getUser();
        $expectedResult = $this->createOrder($user);
        $response = $this->getJson(
            str_replace('{orderId}', $expectedResult['data']['id'], self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['data']);
    }

    public function testGetOrderWithNonExistentOrderShouldFail(): void
    {
        $user = $this->getUser();
        $response = $this->getJson(
            str_replace('{orderId}', 99999, self::USER_CONTROLLER_ROUTE),
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
            str_replace('{orderId}', $orderId, self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    public function testGetOrderByOwnerWithInvalidTokenShouldFail(): void
    {
        $user = $this->getUser();
        $orderId = $this->getUserOrder($user)->id;
        $response = $this->getJson(
            str_replace('{orderId}', $orderId, self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }
}

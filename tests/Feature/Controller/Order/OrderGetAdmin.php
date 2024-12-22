<?php

namespace Feature\Controller\Order;

use Tests\TestCase;

class OrderGetAdmin extends TestCase
{
    private const CONTROLLER_ROUTE = 'api/admin/orders/{orderId}';

    public function testGetOrderByAdminSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->createUser();
        $expectedResult = $this->createOrder($anotherUser);
        $response = $this->getJson(
            str_replace('{orderId}', $expectedResult['data']['id'], self::CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['data']);
    }

    public function testGetOrderByAdminWithNonExistentOrderShouldFail(): void
    {
        $user = $this->getAdminUser();
        $response = $this->getJson(
            str_replace('{orderId}', 99999, self::CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }

    public function testGetOrderByAdminWithInvalidCredentials(): void
    {
        $user = $this->getAdminUser();
        $orderId = $this->getUserOrderId($user);
        $response = $this->getJson(
            str_replace('{orderId}', $orderId, self::CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }
}

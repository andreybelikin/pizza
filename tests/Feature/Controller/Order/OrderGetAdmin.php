<?php

namespace Feature\Controller\Order;

use Tests\TestCase;

class OrderGetAdmin extends TestCase
{
    private const CONTROLLER_ROUTE = 'api/admin/orders/{orderId}';

    public function testGetOrderByAdminSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();
        $expectedOrder = json_decode($this->getUserOrder($anotherUser), true);
        $orderId = $this->getUserOrderId($anotherUser);
        $response = $this->getJson(
            str_replace('{orderId}', $orderId, self::CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedOrder);
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

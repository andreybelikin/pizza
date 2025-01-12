<?php

namespace Tests\Feature\Controller\Order;

use Tests\TestCase;

class OrderGetAdmin extends TestCase
{
    public function testGetOrderByAdminSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->createUser();
        $expectedResult = $this->createOrder($anotherUser);
        $response = $this->getJson(
            route('admin.orders.show', ['orderId' => $expectedResult['data']['id']]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['data']);
    }

    public function testGetOrderByAdminWithNonExistentOrderShouldFail(): void
    {
        $user = $this->getAdminUser();
        $response = $this->getJson(
            route('admin.orders.show', ['orderId' => 99999]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }

    public function testGetOrderByAdminWithInvalidCredentials(): void
    {
        $user = $this->getAdminUser();
        $orderId = $this->getUserOrder($user)->id;
        $response = $this->getJson(
            route('admin.orders.show', ['orderId' => $orderId]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }
}

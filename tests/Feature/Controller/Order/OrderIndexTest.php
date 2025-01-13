<?php

namespace Tests\Feature\Controller\Order;

use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    public function testGetUserOrdersByOwnerSuccess(): void
    {
        $user = $this->createUser();
        $expectedResult = $this->createOrder($user);
        $response = $this->getJson(
            route('users.orders.index', ['userId' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['data']);
        $response->assertJsonFragment($expectedResult['pagination']);
    }

    public function testGetUserEmptyOrders(): void
    {
        $user = $this->createUser();
        $response = $this->getJson(
            route('users.orders.index', ['userId' => $user->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );
        $expectedPagination = [
            'pagination' => [
                'currentPage' => 1,
                'perPage' => 15,
                'total' => 0,
            ]
        ];

        $response->assertOk();
        $response->assertJsonFragment([]);
        $response->assertJsonFragment($expectedPagination);
    }

    public function testGetUserOrdersWithInvalidTokenShouldFail(): void
    {
        $response = $this->getJson(
            route('users.orders.index', ['userId' => $this->getUser()->getKey()]),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testGetUserOrdersByAnotherUserShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();
        $response = $this->getJson(
            route('users.orders.index', ['userId' => $anotherUser->getKey()]),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }
}

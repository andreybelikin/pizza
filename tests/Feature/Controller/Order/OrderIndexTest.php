<?php

namespace Feature\Controller\Order;

use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    private const USER_CONTROLLER_ROUTE = 'api/users/{userId}/orders';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testGetUserOrdersByOwnerSuccess(): void
    {
        $user = $this->createUser();
        $expectedResult = $this->createOrder($user);
        $response = $this->getJson(
            str_replace('{userId}', $user->getKey(), self::USER_CONTROLLER_ROUTE),
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
            str_replace('{userId}', $user->getKey(), self::USER_CONTROLLER_ROUTE),
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
            str_replace('{userId}', $this->getUser()->getKey(), self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testGetUserOrdersByAnotherUserShouldFail(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();
        $response = $this->getJson(
            str_replace('{userId}', $anotherUser->getKey(), self::USER_CONTROLLER_ROUTE),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Controller\Order;

use App\Dto\Request\ListOrderFilterData;
use App\Enums\OrderStatus;
use Tests\TestCase;

class OrderIndexAdminTest extends TestCase
{
    public function testGetFilteredOrdersByAdminSuccess(): void
    {
        $anotherUser = $this->getAnotherUser();
        $filters = new ListOrderFilterData(
            $anotherUser->id,
            'testTitle',
            35000,
            250000,
            OrderStatus::Delivered->value,
            (new \DateTime('today'))->format('d.m.Y')
        );
        $expectedResult = $this->createOrder($anotherUser);

        $response = $this->getJson(
            route('admin.orders.index', (array)$filters),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($this->getAdminUser())]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedResult['pagination']);
        $response->assertJsonFragment($expectedResult['data']);
    }

    public function testGetFilteredOrdersByAdminWithNoResultsSuccess(): void
    {
        $filters = new ListOrderFilterData(
            99,
            'wwwwwwwwwwwwwwwwwwww',
            50000000000,
            50000000000,
            'delivered',
            '24.03.2024'
        );
        $expectedOrders = [
            'data' => [],
        ];
        $expectedPagination = [
            'pagination' => [
                'currentPage' => 1,
                'perPage' => 15,
                'total' => 0,
            ]
        ];

        $response = $this->getJson(
            route('admin.orders.index', (array)$filters),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($this->getAdminUser())]
        );

        $response->assertOk();
        $response->assertJsonFragment($expectedOrders);
        $response->assertJsonFragment($expectedPagination);
    }

    public function testGetOrdersWithEmptyFiltersByAdminSuccess(): void
    {
        $filters = new ListOrderFilterData(
            null,
            null,
            null,
            null,
            null,
            null
        );
        $expectedOrders = json_decode($this->getFilteredOrders($filters), true);

        $response = $this->getJson(
            route('admin.orders.index'),
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($this->getAdminUser())]
        );

        $response->assertOk();
        $response->assertJson($expectedOrders);
    }

    public function testGetFilteredOrdersByAdminWithInvalidTokenShouldFail(): void
    {
        $anotherUser = $this->getAnotherUser();
        $filters = new ListOrderFilterData(
            $anotherUser->id,
            'testTitle',
            35000,
            250000,
            'delivered',
            '24.03.2024'
        );

        $response = $this->getJson(
            route('admin.orders.index', (array)$filters),
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }
}

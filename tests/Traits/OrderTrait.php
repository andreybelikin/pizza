<?php

namespace Tests\Traits;

use App\Dto\Request\ListOrderFilterData;
use App\Enums\OrderStatus;
use App\Enums\ProductType;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;

trait OrderTrait
{
    public function getUserOrderId(User $user): int
    {
        return $user->orders()->first()->id;
    }

    public function getFilteredOrders(ListOrderFilterData $filters): string
    {
        return (new OrdersCollection(
            Order::filter($filters)
                ->with('orderProducts')
                ->paginate(15)
        ))
        ->toJson();
    }

    public function createOrder(User $user): array
    {
        [$attributes, $productsAttributes] = $this->getOrderAttributes($user);
        $order = Order::query()->create($attributes);
        $order->orderProducts()->createMany($productsAttributes);

        return $this->getExpectedResult($order, $attributes);
    }

    private function getOrderAttributes(User $user): array
    {
        $orderAttributes = [
            'user_id' => $user->id,
            'name' => 'testOrderName',
            'phone' => '89956485254',
            'address' => 'testOrderAddress',
            'status' => OrderStatus::DELIVERED->value,
            'total' => 250000 - 35000
        ];

        $productsAttributes = [
            [
                'title' => 'testTitle',
                'description' => 'testDescription',
                'quantity' => 1,
                'type' => ProductType::Drink->value,
                'price' => 107500,
            ],
            [
                'title' => 'testTitle2',
                'description' => 'testDescription2',
                'quantity' => 1,
                'type' => ProductType::Drink->value,
                'price' => 107500,
            ],
        ];

        return [$orderAttributes, $productsAttributes];
    }

    private function getExpectedResult(Order $order, array $orderAttributes): array
    {
        unset($orderAttributes['user_id']);
        unset($orderAttributes['createdAt']);
        $result['data'] = $orderAttributes;
        $result['data']['id'] = $order->id;

        $result['data']['orderProducts'] = $order->orderProducts->map(function (OrderProduct $orderProduct) {
            return [
                'id' => $orderProduct->id,
                'title' => $orderProduct->title,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->price,
                'totalPrice' => $orderProduct->price * $orderProduct->quantity,
            ];
        })->toArray();

        $result['pagination'] = [
            'currentPage' => 1,
            'perPage' => 15,
            'total' => 1,
        ];

        return $result;
    }
}

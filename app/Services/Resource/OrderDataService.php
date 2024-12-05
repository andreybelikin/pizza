<?php

namespace App\Services\Resource;

use App\Enums\OrderStatus;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderDataService
{
    public function getOrder(int $orderId): Order
    {
        $order = Order::query()->find($orderId);

        if (is_null($order)) {
            throw new ResourceNotFoundException();
        }

        return $order;
    }

    public function getFilteredOrders(array $filters): ?LengthAwarePaginator
    {
        return Order::filter($filters)
            ->with()
            ->paginate('orderProducts');
    }

    public function addNewOrder(array $orderData, Collection $orderProducts): void
    {
        $newOrder = new Order($orderData);
        $newOrder->total = $orderProducts->sum(fn ($orderProduct) => $orderProduct->price * $orderProduct->quantity);
        $newOrder->status = OrderStatus::CREATED;
        $newOrder->save();

        $newOrder
            ->orderProducts()
            ->createMany($orderProducts);
    }
}

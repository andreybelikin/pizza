<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

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
}

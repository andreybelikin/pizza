<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderDataService
{
    public function getOrder(int $orderId): Order
    {
        try {
            $order = Order::query()->findOrFail($orderId);
        } catch (ModelNotFoundException) {
            throw new ResourceNotFoundException();
        }

        return $order;
    }

    public function getFilteredOrders(array $filters): Collection
    {
        $orders = Order::filter($filters)->get();

    }
}

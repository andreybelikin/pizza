<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class OrderDataService
{
    public function getOrder(int $orderId): Order
    {
        return Order::query()->findOrFail($orderId);
    }

    public function getUserOrders(): Collection
    {

    }

    public function isOrderExists(int $orderId): void
    {
        if (!Product::query()->where('id', $orderId)->exists()) {
            throw new ResourceNotFoundException();
        }
    }
}

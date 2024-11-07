<?php

namespace App\Services\Resource;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResourceService
{

    public function __construct(
        private OrderDataService $orderDataService
    ) {}

    public function getOrder(int $orderId): JsonResource
    {
        $this->orderDataService->isOrderExists($orderId);
        $order = $this->orderDataService->getOrder($orderId);

        return new OrderResource($order);
    }

    public function getOrders()
    {

    }

    public function addOrder()
    {

    }

    public function updateOrder()
    {

    }

    public function deleteOrder()
    {

    }
}

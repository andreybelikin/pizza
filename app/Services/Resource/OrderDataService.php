<?php

namespace App\Services\Resource;

use App\Enums\OrderStatus;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderDataService
{
    public function getOrder(int $orderId): Order
    {
        $order = Order::query()
            ->with('orderProducts')
            ->find($orderId);

        if (is_null($order)) {
            throw new ResourceNotFoundException();
        }

        return $order;
    }

    public function getFilteredOrders(array $filters): ?LengthAwarePaginator
    {
        return Order::filter($filters)
            ->with('orderProducts')
            ->paginate(15);
    }

    public function getUserOrders(string $userId): ?LengthAwarePaginator
    {
        return Order::query()
            ->with('orderProducts')
            ->where('user_id', $userId)
            ->paginate(15);
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

    public function updateOrder(int $orderId, array $orderData, array $requestOrderProducts): void
    {
        $order = $this->getOrder($orderId);
        $order->update($orderData);

        if (!empty($requestOrderProducts)) {
            foreach ($requestOrderProducts as $requestOrderProduct) {
                $orderProduct = $order->orderProducts()->find($requestOrderProduct['id']);

                if ($requestOrderProduct['quantity'] === 0) {
                    $orderProduct->delete();
                } else {
                    $orderProduct->update($requestOrderProduct);
                }
            }
        }
    }
}

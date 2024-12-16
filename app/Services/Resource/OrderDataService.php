<?php

namespace App\Services\Resource;

use App\Dto\Request\NewOrderData;
use App\Dto\Request\UpdateOrderData;
use App\Enums\OrderStatus;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderDataService
{
    public function __construct(
        private UserDataService $userDataService
    ) {}

    public function getOrder(int $orderId): Order
    {
        return Order::query()
            ->with('orderProducts')
            ->findOrFail($orderId);
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

    public function addNewOrder(NewOrderData $orderData, string $userId): void
    {
        $newOrder = new Order([
            'name' => $orderData->name,
            'address' => $orderData->address ?? $this->userDataService->getDefaultAddress($userId),
            'phone' => $orderData->phone,
            'total' => $orderData->total,
            'status' => $orderData->status ?? OrderStatus::CREATED,
            'user_id' => $userId,
        ]);
        $newOrder->save();
        $newOrder
            ->orderProducts()
            ->createMany($orderData->orderProducts);
    }

    public function updateOrder(int $orderId, UpdateOrderData $orderData): void
    {
        $order = $this->getOrder($orderId);
        $order->update($orderData->getOderInfo());

        if (!empty($orderData->orderProducts)) {
            foreach ($orderData->orderProducts as $orderProduct) {
                $orderProduct = $order->orderProducts()->find($orderProduct['id']);

                if ($orderProduct['quantity'] === 0) {
                    $orderProduct->delete();
                } else {
                    $orderProduct->update($orderProduct);
                }
            }
        }
    }
}

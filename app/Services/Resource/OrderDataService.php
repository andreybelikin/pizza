<?php

namespace App\Services\Resource;

use App\Enums\OrderStatus;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderDataService
{
    public function __construct(
        private CartDataService $cartDataService,
        private UserDataService $userDataService
    ) {}

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

    public function addNewOrder(array $orderData, string $userId): void
    {
        $userCartProducts = $this->cartDataService
            ->setCartUser($userId)
            ->getCartProducts();

        $newOrder = new Order([
            'name' => $orderData['name'],
            'address' => $orderData['address'] ?? $this->userDataService->getDefaultAddress($userId),
            'phone' => $orderData['phone'],
            'total' => $userCartProducts['totalSum'],
            'status' => $orderData['status'] ?? OrderStatus::CREATED,
            'user_id' => $userId,
        ]);
        $newOrder->save();
        $newOrder
            ->orderProducts()
            ->createMany($userCartProducts['products']);
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

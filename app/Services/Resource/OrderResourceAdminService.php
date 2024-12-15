<?php

namespace App\Services\Resource;

use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceAdminService
{
    public function __construct(
        private OrderDataService $orderDataService,
        private CartDataService $cartDataService,
        private UserDataService $userDataService,
    ) {}

    public function getOrder(int $orderId): OrderResource
    {
        $order = $this->orderDataService->getOrder($orderId);

        return new OrderResource($order);
    }

    public function getOrders(OrdersRequest $request): ResourceCollection
    {
        $filters = array_filter(
            $request->only([
                'userId',
                'productTitle',
                'minSum',
                'maxSum',
                'status',
                'createdAt',
            ])
        );
        $orders = $this->orderDataService->getFilteredOrders($filters);

        return new OrdersCollection($orders);
    }

    public function addOrder(OrderAddRequest $request, string $userId): void
    {
        $orderData['data'] = array_filter(
            $request->only([
                'name',
                'phone',
                'address',
                'status',
            ])
        );
        $orderData['products'] = $request->only('orderProducts');

        $this->orderDataService->addNewOrder($orderData, $userId);
        $this->cartDataService->deleteCart();
        $this->userDataService->updateAddress($userId, $orderData['data']['address']);
    }

    public function updateOrder(OrderUpdateRequest $request, string $orderId): void
    {
        $orderData = array_filter(
            $request->only([
                'name',
                'phone',
                'address',
            ])
        );
        $orderProducts = $request->only('orderProducts');
        $this->orderDataService->updateOrder($orderId, $orderData, $orderProducts);
    }
}

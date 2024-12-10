<?php

namespace App\Services\Resource;

use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceService
{
    public function __construct(
        private OrderDataService $orderDataService,
        private CartDataService $cartDataService,
        private UserDataService $userDataService,
    ) {}

    public function getOrder(int $orderId): OrderResource
    {
        $order = $this->orderDataService->getOrder($orderId);
        dd($order);
        auth()
            ->user()
            ->can('get', $order);

        return new OrderResource($order);
    }

    public function getOrders(OrdersRequest $request): ResourceCollection
    {
        auth()
            ->user()
            ->can('get', Order::class);
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
        auth()
            ->user()
            ->can('add', Order::class);
        $requestOrderData = array_filter(
            $request->only([
                'name',
                'phone',
                'address',
            ])
        );
        $userCartProducts = $this->cartDataService
            ->setCartUser($userId)
            ->getCartProducts();
        $this->orderDataService->addNewOrder($requestOrderData, $userCartProducts);
        $this->cartDataService->deleteCart();
        $this->userDataService->updateAddress($userId, $requestOrderData['address']);
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

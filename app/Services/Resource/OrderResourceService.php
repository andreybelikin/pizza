<?php

namespace App\Services\Resource;

use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::authorize('get', $order);

        return new OrderResource($order);
    }

    public function getOrders(string $userId): ResourceCollection
    {
        $requestedUser = $this->userDataService->getUser($userId);
        Gate::authorize('get', $requestedUser);
        $orders = $this->orderDataService->getUserOrders($userId);

        return new OrdersCollection($orders);
    }

    public function addOrder(OrderAddRequest $request, string $userId): void
    {
        $requestedUser = $this->userDataService->getUser($userId);
        Gate::authorize('add', $requestedUser);

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
}

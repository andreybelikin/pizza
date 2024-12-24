<?php

namespace App\Services\Resource;

use App\Dto\OrderProductData;
use App\Dto\Request\NewOrderData;
use App\Http\Requests\OrderAddRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use App\Services\DBTransactionService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceService
{
    public function __construct(
        private OrderDataService $orderDataService,
        private CartDataService $cartDataService,
        private UserDataService $userDataService,
        private DBTransactionService $dbTransactionService,
    ) {}

    public function getOrder(string $orderId): OrderResource
    {
        $order = $this->orderDataService->getOrder((int)$orderId);
        Gate::authorize('get', $order);

        return new OrderResource($order);
    }

    public function getOrders(string $userId): ResourceCollection
    {
        Gate::authorize('index', [Order::class, $userId]);
        $orders = $this->orderDataService->getUserOrders($userId);

        return new OrdersCollection($orders);
    }

    public function addOrder(OrderAddRequest $request, string $userId): JsonResource
    {
        Gate::authorize('add', [Order::class, $userId]);
        $userCart = $this->cartDataService->getCart($userId);

        $orderData = NewOrderData::create(
            request: $request,
            orderProducts: OrderProductData::fromCartProducts($userCart->products),
        );

        $newOrder = $this->dbTransactionService->execute(function () use ($orderData, $userCart) {
            $newOrder = $this->orderDataService->addNewOrder($orderData);
            $this->orderDataService->addCartProducts($newOrder, $orderData->orderProducts);
            $this->cartDataService->deleteCart($orderData->userId);
            $this->userDataService->updateAddress($orderData->userId, $orderData->address);

            return $newOrder;
        });

        return new OrderResource($newOrder->load('orderProducts'));
    }
}

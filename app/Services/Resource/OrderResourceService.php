<?php

namespace App\Services\Resource;

use App\Dto\OrderProductData;
use App\Dto\Request\NewOrderData;
use App\Http\Requests\OrderAddRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
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
    ) {}

    public function getOrder(int $orderId): OrderResource
    {
        $order = $this->orderDataService->getOrder($orderId);
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
            orderProducts: OrderProductData::fromCartProducts($userCart->cartProducts),
            total: $userCart->totalSum
        );

        try {
            DB::beginTransaction();
            $newOrder = $this->orderDataService->addNewOrder($orderData);
            $this->orderDataService->attachCartProductsToOrder($newOrder, $orderData->orderProducts);
            $this->cartDataService->deleteCart($orderData->userId);
            $this->userDataService->updateAddress($orderData->userId, $orderData->address);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return new OrderResource($newOrder->load('orderProducts'));
    }
}

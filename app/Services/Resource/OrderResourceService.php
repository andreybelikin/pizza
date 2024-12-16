<?php

namespace App\Services\Resource;

use App\Dto\Request\NewOrderData;
use App\Http\Requests\OrderAddRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
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

    public function addOrder(OrderAddRequest $request, string $userId): void
    {
        Gate::authorize('add', [Order::class, $userId]);
        $cartProducts = $this->cartDataService->getCartProducts($userId);

        $orderData = new NewOrderData(
            $request->get('name'),
            $request->get('phone'),
            $request->get('address'),
            $request->get('status'),
            $cartProducts['products'],
            $cartProducts['totalSum']
        );

        try {
            DB::beginTransaction();
            $this->orderDataService->addNewOrder($orderData, $userId);
            $this->cartDataService->deleteCart($userId);
            $this->userDataService->updateAddress($userId, $orderData->address);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}

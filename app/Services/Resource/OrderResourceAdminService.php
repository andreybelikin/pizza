<?php

namespace App\Services\Resource;

use App\Dto\Request\NewOrderData;
use App\Dto\Request\UpdateOrderData;
use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

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
        $orderData = new NewOrderData(
            $request->get('name'),
            $request->get('phone'),
            $request->get('address'),
            $request->get('status'),
            $request->get('orderProducts'),
            array_sum(array_column($request->get('orderProducts'), 'totalPrice'))
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

    public function updateOrder(OrderUpdateRequest $request, string $orderId): void
    {
        $orderData = UpdateOrderData::fromRequest($request);

        try {
            DB::beginTransaction();
            $this->orderDataService->updateOrder($orderId, $orderData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}

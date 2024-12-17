<?php

namespace App\Services\Resource;

use App\Dto\OrderProductData;
use App\Dto\Request\NewOrderData;
use App\Dto\Request\UpdateOrderData;
use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
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

    public function addOrder(OrderAddRequest $request): JsonResource
    {
        $orderProductsData = OrderProductData::fromRequest($request->get('orderProducts'));
        $orderData = NewOrderData::create(
            request: $request,
            orderProducts: $orderProductsData,
            total: $this->getOrderTotal($orderProductsData)
        );

        try {
            DB::beginTransaction();
            $newOrder = $this->orderDataService->addNewOrder($orderData);
            $this->orderDataService->attachProductsToOrder($newOrder, $orderData->orderProducts);
            $this->cartDataService->deleteCart($orderData->userId);
            $this->userDataService->updateAddress($orderData->userId, $orderData->address);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return new OrderResource($newOrder->load('orderProducts'));
    }

    public function updateOrder(OrderUpdateRequest $request): JsonResource
    {
        $requestProducts = OrderProductData::fromRequest($request->get('orderProducts'));
        $orderData = UpdateOrderData::create(
            request: $request,
            orderProducts: $requestProducts,
            total: $this->getOrderTotal($requestProducts)
        );

        try {
            DB::beginTransaction();
            $order = $this->orderDataService->updateOrder($orderData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return new OrderResource($order->load('orderProducts'));
    }

    private function getOrderTotal(Collection $products): int
    {
        return $products->sum(fn ($product) => $product->price * $product->quantity);
    }
}

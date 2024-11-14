<?php

namespace App\Services\Resource;

use App\Dto\Response\Controller\OrderResponseDto;
use App\Http\Requests\OrdersRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class OrderResourceService
{

    public function __construct(
        private OrderDataService $orderDataService
    ) {}

    public function getOrder(int $orderId): OrderResponseDto
    {
        $order = $this->orderDataService->getOrder($orderId);
        $orderProducts = $this->prepareOrderProducts($order);
        $totalPrice = $this->prepareTotalPrice($orderProducts);

        return new OrderResponseDto(
            $order->name,
            $order->phone,
            $order->address,
            $order->status,
            $orderProducts->toArray(),
            $totalPrice
        );
    }

    private function prepareOrderProducts(Order $order): Collection
    {
        return $order->orderProducts->map(function (OrderProduct $orderProduct) {
            return [
                'title' => $orderProduct->title,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->price,
                'priceSum' => $orderProduct->quantity * $orderProduct->price,
            ];
        });
    }

    private function prepareTotalPrice(Collection $orderProducts): float
    {
        return $orderProducts->pluck('priceSum')->sum();
    }

    public function getOrders(OrdersRequest $request): Collection
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

    }

    public function addOrder()
    {

    }

    public function updateOrder()
    {

    }

    public function deleteOrder()
    {

    }
}

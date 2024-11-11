<?php

namespace App\Services\Resource;

use App\Dto\Response\Controller\OrderResponseDto;
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
        $this->orderDataService->isOrderExists($orderId);
        $order = $this->orderDataService->getOrder($orderId);
        $orderProducts = $this->prepareOrderProducts($order);

        return new OrderResponseDto(
            $order->name,
            $order->phone,
            $order->address,
            $order->status,
            $orderProducts->toArray(),
            $order->total,
        );
    }

    private function prepareOrderProducts(Order $order): Collection
    {
        return $order->orderProducts->map(function (OrderProduct $orderProduct) {
            return [
                'title' => $orderProduct->title,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->price,
                'totalPrice' => $orderProduct->quantity * $orderProduct->price,
            ];
        });
    }

    private function getTotalPrice(Collection $orderProducts): float
    {

    }

    public function getOrders()
    {

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

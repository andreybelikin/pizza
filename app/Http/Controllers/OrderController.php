<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderAddRequest;
use App\Services\Resource\OrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(string $userId): JsonResponse
    {
        $orders = $this->orderService->getOrders($userId);

        return response()->json($orders);
    }

    public function get(string $orderId): JsonResponse
    {
        $order = $this->orderService->getOrder($orderId);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function add(OrderAddRequest $request, string $userId): JsonResponse
    {
        $order = $this->orderService->addOrder($request, $userId);

        return response()
            ->json($order, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

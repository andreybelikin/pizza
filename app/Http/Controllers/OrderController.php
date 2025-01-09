<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderAddRequest;
use App\Services\Resource\OrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    public function __construct(
        private OrderService $orderResourceService
    ) {}

    public function index(string $userId): JsonResponse
    {
        $orders = $this->orderResourceService->getOrders($userId);

        return response()->json($orders);
    }

    public function get(string $orderId): JsonResponse
    {
        $order = $this->orderResourceService->getOrder($orderId);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function add(OrderAddRequest $request, string $userId): JsonResponse
    {
        $order = $this->orderResourceService->addOrder($request, $userId);

        return response()
            ->json($order, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Resource\OrderResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController
{
    public function __construct(
        private OrderResourceService  $orderResourceService
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
}

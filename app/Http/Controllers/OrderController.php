<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Services\Resource\OrderResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController
{
    public function __construct(
        private OrderResourceService  $orderResourceService
    ) {}

    public function index(OrdersRequest $request): JsonResponse
    {
        $orders = $this->orderResourceService->getOrders($request);

        return response()
            ->json($orders)
            ->setStatusCode(JsonResponse::HTTP_OK);
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
        $this->orderResourceService->addOrder($request, $userId);

    }

    public function update(): JsonResponse
    {

    }

    public function delete(): JsonResponse
    {

    }
}

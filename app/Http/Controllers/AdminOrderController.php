<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderAddRequest;
use App\Http\Requests\Order\OrdersRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Services\Resource\OrderAdminService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminOrderController
{
    public function __construct(private OrderAdminService $orderAdminService)
    {}

    public function index(OrdersRequest $request): JsonResponse
    {
        $orders = $this->orderAdminService->getOrders($request);

        return response()
            ->json($orders)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function show(string $orderId): JsonResponse
    {
        $order = $this->orderAdminService->getOrder($orderId);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function store(OrderAddRequest $request): JsonResponse
    {
        $order = $this->orderAdminService->addOrder($request);

        return response()
            ->json($order, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(OrderUpdateRequest $request): JsonResponse
    {
        $order = $this->orderAdminService->updateOrder($request);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

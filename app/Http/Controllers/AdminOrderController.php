<?php

namespace App\Http\Controllers;

use App\Dto\Response\Resourse\CreatedResourceDto;
use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Services\Resource\OrderResourceAdminService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminOrderController
{
    public function __construct(private OrderResourceAdminService $orderResourceAdminService)
    {}

    public function index(OrdersRequest $request): JsonResponse
    {
        $orders = $this->orderResourceAdminService->getOrders($request);

        return response()
            ->json($orders)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get(string $orderId): JsonResponse
    {
        $order = $this->orderResourceAdminService->getOrder($orderId);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function add(OrderAddRequest $request): JsonResponse
    {
        $order = $this->orderResourceAdminService->addOrder($request);

        return response()
            ->json($order, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(OrderUpdateRequest $request): JsonResponse
    {
        $order = $this->orderResourceAdminService->updateOrder($request);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

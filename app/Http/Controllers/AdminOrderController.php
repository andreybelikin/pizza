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

    public function add(OrderAddRequest $request, string $userId): Response
    {
        $this->orderResourceAdminService->addOrder($request, $userId);
        $responseDto = new CreatedResourceDto();

        return response()->json($responseDto->toArray(), Response::HTTP_CREATED);
    }

    public function update(OrderUpdateRequest $request, string $orderId): Response
    {
        $this->orderResourceAdminService->updateOrder($request, $orderId);

        return response();
    }
}

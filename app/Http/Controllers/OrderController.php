<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Services\Resource\OrderResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
            ->setStatusCode(Response::HTTP_OK);
    }

    public function get(string $orderId): JsonResponse
    {
        $order = $this->orderResourceService->getOrder($orderId);

        return response()
            ->json($order)
            ->setStatusCode(Response::HTTP_OK)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function add(OrderAddRequest $request, string $userId): Response
    {
        $this->orderResourceService->addOrder($request, $userId);

        return response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(OrderUpdateRequest $request, string $orderId): Response
    {
        $this->orderResourceService->updateOrder($request, $orderId);

        return response()->setStatusCode(Response::HTTP_OK);
    }
}

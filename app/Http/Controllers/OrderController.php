<?php

namespace App\Http\Controllers;

use App\Dto\Response\Resourse\CreatedResourceDto;
use App\Http\Requests\OrderAddRequest;
use App\Services\Resource\OrderResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function add(OrderAddRequest $request, string $userId): Response
    {
        $this->orderResourceService->addOrder($request, $userId);
        $responseDto = new CreatedResourceDto();

        return response()->json($responseDto->toArray(), Response::HTTP_CREATED);
    }

}

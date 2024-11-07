<?php

namespace App\Http\Controllers;

use App\Services\Resource\OrderResourceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController
{
    public function __construct(
        private OrderResourceService  $orderResourceService
    ) {}

    public function index(Request $request): JsonResponse
    {

    }

    public function get(string $orderId): JsonResponse
    {
        $order = $this->orderResourceService->getOrder($orderId);

        return response()
            ->json($order)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function getUserOrders(): JsonResponse
    {

    }

    public function add(string $id): JsonResponse
    {

    }

    public function update(): JsonResponse
    {

    }

    public function delete(): JsonResponse
    {

    }
}

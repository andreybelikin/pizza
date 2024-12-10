<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpdateRequest;
use App\Services\Resource\OrderResourceService;
use Symfony\Component\HttpFoundation\Response;

class AdminOrderController
{
    public function __construct(private OrderResourceService $orderResourceService)
    {}

    public function update(OrderUpdateRequest $request, string $orderId): Response
    {
        $this->orderResourceService->updateOrder($request, $orderId);

        return response()->setStatusCode(Response::HTTP_OK);
    }
}

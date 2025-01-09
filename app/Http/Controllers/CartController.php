<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartUpdateRequest;
use App\Services\Resource\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CartController
{
    public function __construct(
        private CartService $cartResourceService
    ) {}

    public function get(string $userId): JsonResponse
    {
        $cart = $this->cartResourceService->getCart($userId);

        return response()
            ->json($cart)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(CartUpdateRequest $request, string $userId): JsonResponse
    {
        $cart = $this->cartResourceService->updateCart($request, $userId);

        return response()
            ->json($cart)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $userId): Response
    {
        $this->cartResourceService->deleteCart($userId);

        return response('');
    }
}

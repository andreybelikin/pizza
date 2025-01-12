<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartUpdateRequest;
use App\Services\Resource\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminCartController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function show(string $userId): JsonResponse
    {
        $cart = $this->cartService->getCart($userId);

        return response()
            ->json($cart)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(CartUpdateRequest $request): JsonResponse
    {
        $cart = $this->cartService->updateCart($request);

        return response()
            ->json($cart)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function destroy(string $userId): Response
    {
        $this->cartService->deleteCart($userId);

        return response('');
    }
}

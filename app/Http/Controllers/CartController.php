<?php

namespace App\Http\Controllers;

use App\Dto\Response\Resourse\CartLimitExceptionResponseDto;
use App\Dto\Response\Resourse\DeletedResourceDto;
use App\Exceptions\Resource\Cart\QuantityPerTypeLimitException;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Services\Resource\CartResourceService;
use Illuminate\Http\JsonResponse;

class CartController
{
    public function __construct(private CartResourceService $cartResourceService) {}

    public function get(string $userId): JsonResponse
    {
        $cart = $this->cartResourceService
            ->setCartUser($userId)
            ->getCart()
        ;

        return response()
            ->json($cart)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function update(CartUpdateRequest $request, string $userId): JsonResponse
    {
        try {
            $cart = $this->cartResourceService
                ->setCartUser($userId)
                ->updateCart($request)
            ;
            $response = response()
                ->json($cart)
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ;
        } catch (QuantityPerTypeLimitException $exception) {
            $responseDto = new CartLimitExceptionResponseDto($exception->violations);
            $response = response()->json($responseDto->toArray(), $responseDto::STATUS);
        }

        return $response;
    }

    public function delete(string $userId): JsonResponse
    {
        $this->cartResourceService
            ->setCartUser($userId)
            ->deleteCart()
        ;
        $responseDto = new DeletedResourceDto();

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }
}

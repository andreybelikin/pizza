<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartAddRequest;
use App\Services\Resource\CartResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController
{
    public function __construct(private CartResourceService $cartService) {}

    public function index(Request $request): JsonResponse
    {

    }

    public function get(Request $request): JsonResponse
    {

    }

    public function add(CartAddRequest $request): JsonResponse
    {
        $this->cartService->createItems($request);
    }

    public function update(): JsonResponse
    {

    }
}

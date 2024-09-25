<?php

namespace App\Http\Controllers\Cart;

use App\Services\Resource\CartResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController
{
    public function __construct(private CartResourceService $cartService) {}

    public function create(Request $request): JsonResponse
    {
        $this->cartService->createItems($request);
    }

    public function index(Request $request)
    {

    }
}

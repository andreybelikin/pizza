<?php

namespace App\Http\Controllers\Profile;

use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function create(Request $request): JsonResponse
    {
        $this->cartService->createItems($request);
    }

    public function index(Request $request)
    {

    }
}

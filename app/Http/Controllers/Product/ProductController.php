<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController
{
    public function index(): JsonResponse
    {
        return response()->json(Product::all());
    }
    public function get(string $id): JsonResponse
    {
        try {
            $product = response()->json(Product::query()->find($id));
        } catch (ModelNotFoundException) {
            response()
                ->json(['error' => sprintf('Product № %s in not found', $id)])
                ->setStatusCode(404)
            ;
        }

        return $product;
    }
}

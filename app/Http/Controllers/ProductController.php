<?php

namespace App\Http\Controllers;

use App\Services\Resource\ProductResourceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController
{
    public function __construct(private ProductResourceService $productResourceService)
    {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productResourceService->getProducts($request);

        return response()
            ->json($products)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get(string $id): JsonResponse
    {
        $product = $this->productResourceService->getProduct($id);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

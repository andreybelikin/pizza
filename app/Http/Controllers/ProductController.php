<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
use App\Services\Resource\ProductService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController
{
    public function __construct(private ProductService $productService)
    {}

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productService->getProducts($request);

        return response()
            ->json($products)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function show(string $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}

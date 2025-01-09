<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
use App\Services\Resource\ProductService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController
{
    public function __construct(private ProductService $productResourceService)
    {}

    public function index(ProductIndexRequest $request): JsonResponse
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

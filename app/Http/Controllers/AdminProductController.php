<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Services\Resource\ProductAdminService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminProductController
{
    public function __construct(private ProductAdminService $productResourceAdminService)
    {}

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productResourceAdminService->getProducts($request);

        return response()
            ->json($products)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get(string $id): JsonResponse
    {
        $product = $this->productResourceAdminService->getProduct($id);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function add(ProductAddRequest $request): JsonResponse
    {
        $createdProduct = $this->productResourceAdminService->addProduct($request);

        return response()
            ->json($createdProduct)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        $updatedProduct = $this->productResourceAdminService->updateProduct($request);

        return response()
            ->json($updatedProduct)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $id): Response
    {
        $this->productResourceAdminService->deleteProduct($id);

        return response('');
    }
}

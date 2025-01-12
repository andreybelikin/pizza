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
    public function __construct(private ProductAdminService $productAdminService)
    {}

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $products = $this->productAdminService->getProducts($request);

        return response()
            ->json($products)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function show(string $id): JsonResponse
    {
        $product = $this->productAdminService->getProduct($id);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function store(ProductAddRequest $request): JsonResponse
    {
        $createdProduct = $this->productAdminService->addProduct($request);

        return response()
            ->json($createdProduct)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        $updatedProduct = $this->productAdminService->updateProduct($request);

        return response()
            ->json($updatedProduct)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function destroy(string $id): Response
    {
        $this->productAdminService->deleteProduct($id);

        return response('');
    }
}

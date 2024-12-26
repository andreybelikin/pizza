<?php

namespace App\Http\Controllers;

use App\Services\Resource\ProductResourceAdminService;
use Illuminate\Http\Request;

class AdminProductController
{
    public function __construct(private ProductResourceAdminService $productResourceAdminService)
    {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productResourceService->getProducts($request);

        return response()
            ->json($products)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ;
    }

    public function get(string $id): JsonResponse
    {
        $product = $this->productResourceService->getProduct($id);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ;
    }

    public function add(ProductAddRequest $request): JsonResponse
    {
        $this->productResourceService->addProduct($request);
        $responseDto = new CreatedResourceDto();

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        $product = $this->productResourceService->updateProduct($request);

        return response()
            ->json($product)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ;
    }

    public function delete(ProductDeleteRequest $request): JsonResponse
    {
        $this->productResourceService->deleteProduct($request);
        $responseDto = new DeletedResourceDto();

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }
}

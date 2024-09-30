<?php

namespace App\Http\Controllers;

use App\Dto\Response\Resourse\CreatedResourceDto;
use App\Dto\Response\Resourse\DeletedResourceDto;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductDeleteRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
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

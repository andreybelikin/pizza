<?php

namespace App\Services\Resource;

use App\Dto\Request\AddProductData;
use App\Dto\Request\ListProductFilterData;
use App\Dto\Request\UpdateProductData;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Services\DBTransactionService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResourceAdminService
{
    public function __construct(
        private ProductDataService $productDataService,
        private DBTransactionService $dbTransactionService,
    )
    {}

    public function getProduct(string $productId): JsonResource
    {
        $requestedProductResource = $this->productDataService->getProduct($productId);

        return new ProductResource($requestedProductResource);
    }

    public function getProducts(ProductIndexRequest $request): ResourceCollection
    {
        $listProductFilterData = ListProductFilterData::fromRequest($request);
        $products = $this->productDataService->getFilteredProducts($listProductFilterData);

        return new ProductsCollection($products);
    }

    public function addProduct(ProductAddRequest $request): JsonResource
    {
        $addProductData = AddProductData::fromRequest($request);
        $createdProduct = $this->dbTransactionService->execute(function () use ($addProductData) {
            return $this->productDataService->addProduct($addProductData);
        });

        return new ProductResource($createdProduct);
    }

    public function updateProduct(ProductUpdateRequest $request): JsonResource
    {
        $updateProductData = UpdateProductData::fromRequest($request);
        $updatedProduct = $this->dbTransactionService->execute(function () use ($updateProductData) {
            return $this->productDataService->updateProduct($updateProductData);
        });

        return new ProductResource($updatedProduct);
    }

    public function deleteProduct(string $productId): void
    {
        $this->dbTransactionService->execute(fn () => $this->productDataService->deleteProduct($productId));
    }
}

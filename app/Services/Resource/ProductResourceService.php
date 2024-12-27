<?php

namespace App\Services\Resource ;

use App\Dto\Request\ListProductFilterData;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResourceService
{
    public function __construct(private ProductDataService $productDataService)
    {}

    public function getProduct(string $requestedProductId): JsonResource
    {
        $product = $this->productDataService->getProduct($requestedProductId);

        return new ProductResource($product);
    }

    public function getProducts(ProductIndexRequest $request): ResourceCollection
    {
        $listProductFilterData = ListProductFilterData::fromRequest($request);
        $products = $this->productDataService->getFilteredProducts($listProductFilterData);

        return new ProductsCollection($products);
    }
}

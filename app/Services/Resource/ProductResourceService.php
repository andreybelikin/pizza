<?php

namespace App\Services\Resource ;

use App\Dto\Request\AddProductData;
use App\Dto\Request\DeleteProductData;
use App\Dto\Request\ListProductFilterData;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductDeleteRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Models\Product;
use App\Services\Resource\Abstract\ResourceServiceAbstract;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResourceService extends ResourceServiceAbstract
{
    public function __construct(private ProductDataService $productDataService) {
        parent::__construct();
        parent::setResourceModel(Product::class);
    }

    public function getProduct(string $requestedProductId): JsonResource
    {
        $getProductData = ListProductFilterData::fromRequest($requestedProductId);
        /** @var Product $requestedProductResource */
        $requestedProductResource = $this->getRequestedResource($requestedProductId);

        return new ProductResource($requestedProductResource);
    }

    public function getProducts(ProductIndexRequest $request): ResourceCollection
    {
        $listProductFilterData = ListProductFilterData::fromRequest($request);
        $products = $this->productDataService->getFilteredProducts($listProductFilterData);

        return new ProductsCollection($products);
    }
}

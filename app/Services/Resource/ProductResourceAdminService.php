<?php

namespace App\Services\Resource;

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

class ProductResourceAdminService extends ResourceServiceAbstract
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

    public function addProduct(ProductAddRequest $request): void
    {
        $addProductData = AddProductData::fromRequest($request);

        Gate::authorize('add', [Product::class, $addProductData->userId]);

        $newProduct = new Product($addProductData);
        $newProduct->save();
    }

    public function updateProduct(ProductUpdateRequest $request): JsonResource
    {
        $addProductData = AddProductData::fromRequest($request);
        Gate::authorize('update', [Product::class, ]);

        $newData = $this->getProductData($request);
        $this->updateResource($requestedProductResource, $newData);

        return new JsonResource($requestedProductResource);
    }

    public function deleteProduct(ProductDeleteRequest $request): void
    {
        $deleteProductData = DeleteProductData::fromRequest($request);
        $requestedProductResource = $this->getRequestedResource($deleteProductData->id);
        Gate::authorize('delete', [Product::class, ]);
        $this->deleteResource($requestedProductResource);
    }
}

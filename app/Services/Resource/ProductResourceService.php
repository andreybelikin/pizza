<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductDeleteRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Models\Product;
use App\Models\User;
use App\Services\Resource\Abstract\ResourceServiceAbstract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductResourceService extends ResourceServiceAbstract
{
    private const PRODUCTS_PER_PAGE = 15;

    public function __construct(private CachedResourceService $cachedResourceService) {
        parent::__construct($this->cachedResourceService);
        parent::setResourceModel(Product::class);
    }

    public function getProduct(string $requestedProductId): JsonResource
    {
        /** @var Product $requestedProductResource */
        $requestedProductResource = $this->getRequestedResource($requestedProductId);

        return new ProductResource($requestedProductResource);
    }

    public function getProducts(Request $request): ResourceCollection
    {
        $products = $this->getFilteredProducts($request);

        if (is_null($products)) {
            $products = $this->getProductsAsIs();
        }

        return new ProductsCollection($products);
    }

    public function addProduct(ProductAddRequest $request): void
    {
        $productData = $this->getProductData($request);

        $this->checkActionPermission('add');

        $newProduct = new Product($productData);
        $newProduct->save();
    }

    public function updateProduct(ProductUpdateRequest $request): JsonResource
    {
        $requestedProductResource = $this->getRequestedResource($request->input('id'));
        $this->checkActionPermission('update');

        $newData = $this->getProductData($request);
        $this->updateResource($requestedProductResource, $newData);

        return new JsonResource($requestedProductResource);
    }

    public function deleteProduct(ProductDeleteRequest $request): void
    {
        $requestedProductResource = $this->getRequestedResource($request->input('id'));
        $this->checkActionPermission('delete');
        $this->deleteResource($requestedProductResource);
    }

    private function checkActionPermission(string $resourceAction): void
    {
        $authorizedUser = auth()->user();

        if ($authorizedUser->cant($resourceAction, Product::class)) {
            throw new ResourceAccessException();
        }
    }

    private function getProductData(FormRequest $request): array
    {
        return array_filter(
            $request->only([
                'title',
                'description',
                'type',
                'price',
            ])
        );
    }

    private function getFilteredProducts(Request $request): ?LengthAwarePaginator
    {
        $filters = $this->getProductsFilters($request);

        if (!empty($filters)) {
            return Product::filter($filters)->paginate(self::PRODUCTS_PER_PAGE);
        }

        return null;
    }

    private function getProductsAsIs(): LengthAwarePaginator
    {
        return Product::query()->paginate(self::PRODUCTS_PER_PAGE);
    }

    private function getProductsFilters(Request $request): array
    {
        return array_filter(
            $request->only([
                'title',
                'description',
                'type',
                'minPrice',
                'maxPrice',
            ])
        );
    }
}

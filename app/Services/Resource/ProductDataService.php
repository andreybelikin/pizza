<?php

namespace App\Services\Resource;

use App\Dto\Request\AddProductData;
use App\Dto\Request\ListProductFilterData;
use App\Dto\Request\UpdateProductData;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductDataService
{
    private const PRODUCTS_PER_PAGE = 15;

    public function getProduct(string $id): Product
    {
        return Product::query()->findOrFail($id);
    }

    public function addProduct(AddProductData $addProductData): Product
    {
        $newProduct = new Product($addProductData->toArray());
        $newProduct->save();

        return $newProduct;
    }

    public function updateProduct(UpdateProductData $updateProductData): Product
    {
        $product = Product::query()->findOrFail($updateProductData->id);
        $product->update($updateProductData->getProductInfo());
        $product->refresh();

        return $product;
    }

    public function deleteProduct(string $id): void
    {
        Product::query()->findOrFail($id)->delete();
    }

    public function getProductsById(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }

    public function getProductsTypes(Collection $productsIds): Collection
    {
        return Product::query()
            ->select(['id', 'type'])
            ->whereIn('id', $productsIds)
            ->get();
    }

    public function getFilteredProducts(ListProductFilterData $filters): ?LengthAwarePaginator
    {
        return Product::filter($filters)->paginate(self::PRODUCTS_PER_PAGE);
    }
}

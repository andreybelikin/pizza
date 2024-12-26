<?php

namespace App\Services\Resource;

use App\Dto\Request\ListProductFilterData;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductDataService
{
    private const PRODUCTS_PER_PAGE = 15;

    public function getProductsById(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }

    public function getProductsTypes(Collection $products): Collection
    {
        $ids = $products->pluck('id');

        return Product::query()
            ->select(['id', 'type'])
            ->whereIn('id', $ids)
            ->get();
    }

    public function getFilteredProducts(ListProductFilterData $filters): ?LengthAwarePaginator
    {
        return Product::filter($filters)->paginate(self::PRODUCTS_PER_PAGE);
    }
}

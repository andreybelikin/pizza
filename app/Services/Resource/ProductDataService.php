<?php

namespace App\Services\Resource;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductDataService
{
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
}

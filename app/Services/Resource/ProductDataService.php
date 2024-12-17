<?php

namespace App\Services\Resource;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductDataService
{
    public function getProductsById(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }
}

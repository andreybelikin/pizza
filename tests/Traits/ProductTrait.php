<?php

namespace Tests\Traits;

use App\Models\Product;
use Illuminate\Support\Collection;

trait ProductTrait
{
    public function getProductsForNewOrder(): Collection
    {
        return Product::query()
            ->take(2)
            ->get();
    }
}

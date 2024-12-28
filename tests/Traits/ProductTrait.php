<?php

namespace Tests\Traits;

use App\Models\Product;
use Illuminate\Support\Collection;

trait ProductTrait
{
    const RESULT_KEYS = [
        'id',
        'title',
        'description',
        'type',
        'price',
    ];

    public function getNullProduct(int $id): null
    {
        return Product::query()->find($id);
    }

    public function getProductsForNewOrder(): Collection
    {
        return Product::query()
            ->take(2)
            ->get();
    }

    public function getRandomProduct(): Product
    {
        return Product::query()->first();
    }

    public function getProducts(): array
    {
        return Product::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'type' => $product->type,
                'price' => $product->price,
            ];
        })->toArray();
    }

    public function createProduct(): Product
    {
        return Product::factory()->createOne();
    }

    public function createProducts(array $productsValues): array
    {
        if (empty($productsValues)) {
            $products = Product::factory()
                ->count(3)
                ->create()
                ->only(self::RESULT_KEYS);
        } else {
            $products = array_map(
                fn ($productValues) => Product::factory()
                    ->create($productValues)
                    ->only(self::RESULT_KEYS),
                $productsValues
            );
        }

        return $products;
    }
}

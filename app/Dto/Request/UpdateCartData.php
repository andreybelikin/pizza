<?php

namespace App\Dto\Request;

use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Support\Collection;

readonly class UpdateCartData
{
    public function __construct(
        public int $userId,
        public Collection $products,
    ) {}

    public static function fromRequest(ProductUpdateRequest $request): self
    {
        $products = collect(
            array_map(function ($product) {
                return new UpdateCartProductData(
                    id: $product['id'],
                    quantity: $product['quantity'],
                );
            }, $request->get('products')),
        );

        return new self(
            userId: $request->route('userId'),
            products: $products
        );
    }
}

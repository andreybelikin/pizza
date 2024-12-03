<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductsCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->transform(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'type' => $product->type,
                    'price' => $product->price,
                    'createdAt' => $product->created_at,
                    'updatedAt' => $product->updated_at,
                ];
            }),
            'pagination' => [
                'perPage' => $this->resource->perPage(),
                'currentPage' => $this->resource->currentPage(),
                'total' => $this->resource->total(),
            ],
        ];
    }
}

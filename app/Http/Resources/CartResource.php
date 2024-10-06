<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => $this->items->map(function ($product) {
                return [
                    'title' => $product->title,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                ];
            })
        ];
    }
}

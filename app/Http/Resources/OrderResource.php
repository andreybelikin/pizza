<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'products' => OrderProductResource::collection($this->whenLoaded('orderProducts')),
            'total' => $this->getProductsTotalPrice(),
        ];
    }

    private function getProductsTotalPrice(): float
    {
        return $this->orderProducts->sum(fn ($product) => $product->price * $product->quantity);
    }
}

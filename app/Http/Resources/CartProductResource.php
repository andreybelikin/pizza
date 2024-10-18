<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'quantity' => $this['quantity'],
            'title' => $this['title'],
            'price' => ($this['price']),
            'totalPrice' => $this['totalPrice'],
        ];
    }
}

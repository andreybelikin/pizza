<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): \Illuminate\Support\Collection
    {
        if ($this->resource instanceof Collection) {
            return $this->resource->map(function (OrderProduct $orderProduct) {
                return [
                    'title' => $orderProduct['title'],
                    'quantity' => $orderProduct['quantity'],
                    'price' => $orderProduct['price'],
                ];
            });
        }
    }
}

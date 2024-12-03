<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => OrderResource::collection($this->collection),
            'pagination' => [
                'perPage' => $this->resource->perPage(),
                'currentPage' => $this->resource->currentPage(),
                'total' => $this->resource->total(),
            ]
        ];
    }
}

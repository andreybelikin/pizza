<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $orderProductCollection = OrderProductResource::collection($this->orderProducts);

        return [
            'id' => $this->id,
            'name' => $this->title,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'products' => $orderProductCollection,
            'total' => $this->getTotal($orderProductCollection),
        ];
    }

    public function getTotal(Collection $orderProducts): float
    {
        $result = $orderProducts->map(fn (OrderProduct $orderProduct) => dd($orderProduct));
        dd($result);

        return $result->sum();
    }
}

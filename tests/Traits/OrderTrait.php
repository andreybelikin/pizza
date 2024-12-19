<?php

namespace Tests\Traits;

use App\Http\Resources\OrderResource;
use App\Models\User;

trait OrderTrait
{
    public function getOrders(User $user): string
    {
        return OrderResource::collection(
            $user
            ->orders()
            ->with('orderProducts')
            ->paginate(15)
        )
        ->toJson();
    }
}

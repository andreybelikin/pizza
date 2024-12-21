<?php

namespace Tests\Traits;

use App\Dto\Request\ListOrderFilterData;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use App\Models\User;

trait OrderTrait
{
    public function getUserOrders(User $user): string
    {
        return OrderResource::collection(
            $user
            ->orders()
            ->with('orderProducts')
            ->paginate(15)
        )
        ->toJson();
    }

    public function getUserOrder(User $user): string
    {
        return (new OrderResource(
            $user->orders()
                ->with('orderProducts')
                ->first()
        ))
        ->toJson();
    }

    public function getUserOrderId(User $user): int
    {
        return $user->orders()->first()->id;
    }

    public function getFilteredOrders(ListOrderFilterData $filters): string
    {
        return (new OrdersCollection(
            Order::filter($filters)
                ->with('orderProducts')
                ->paginate(15)
        ))
        ->toJson();
    }

    public function changeOrderProducts(User $user, ListOrderFilterData $filterData): void
    {
        $orders = $user->orders()
            ->with('orderProducts')
            ->get();
        $order = $orders->first();
        $order->update([
            'status' => $filterData->status,
            'total' => $filterData->maxTotal - $filterData->minTotal,
            'createdAt' => \DateTime::createFromFormat('d.m.Y', $filterData->createdAt)
                ->format('Y-m-d')
        ]);
        $order->orderProducts()
            ->first()
            ->update(['title' => $filterData->productTitle]);
    }
}

<?php

namespace App\Services\Resource;

use App\Dto\OrderProductData;
use App\Dto\Request\NewOrderData;
use App\Dto\Request\UpdateOrderData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderDataService
{
    public function __construct(
        private UserDataService $userDataService,
        private ProductDataService $productDataService
    ) {}

    public function getOrder(int $orderId): Order
    {
        return Order::query()
            ->with('orderProducts')
            ->findOrFail($orderId);
    }

    public function getFilteredOrders(array $filters): ?LengthAwarePaginator
    {
        return Order::filter($filters)
            ->with('orderProducts')
            ->paginate(15);
    }

    public function getUserOrders(string $userId): ?LengthAwarePaginator
    {
        return Order::query()
            ->with('orderProducts')
            ->where('user_id', $userId)
            ->paginate(15);
    }

    public function addNewOrder(NewOrderData $orderData): Order
    {
        $newOrder = new Order([
            'name' => $orderData->name,
            'address' => $orderData->address ?? $this->userDataService->getDefaultAddress($orderData->userId),
            'phone' => $orderData->phone,
            'total' => $orderData->total,
            'status' => $orderData->status ?? OrderStatus::CREATED,
            'user_id' => $orderData->userId,
        ]);
        $newOrder->save();

        return $newOrder;
    }

    public function updateOrder(UpdateOrderData $updateOrderData): Order
    {
        $order = $this->getOrder($updateOrderData->id);
        $order->update($updateOrderData->getOderInfo());

        if (!is_null($updateOrderData->orderProducts)) {
            foreach ($updateOrderData->orderProducts as $product) {
                $orderProductModel = $order->orderProducts()->findOrFail($product->id);

                if ($orderProductModel->quantity === 0) {
                    $orderProductModel->delete();
                } else {
                    $orderProductModel->update($product);
                }
            }
        }

        return $order;
    }

    public function attachProductsToOrder(Order $newOrder, Collection $requestOrderProductsData): void
    {
        $ids = $requestOrderProductsData->pluck('id')->toArray();
        $productsModels = $this->productDataService->getProductsById($ids);
        $productsToAttach = $productsModels->map(
            function (Product $productModel) use ($requestOrderProductsData) {
                $quantity = $requestOrderProductsData->where('id', $productModel->id)->get('quantity');
                $productModel->quantity = $quantity;
            }
        );

        $newOrder->orderProducts()->createMany($productsToAttach->toArray());
    }

    public function attachCartProductsToOrder(Order $newOrder, Collection $cartProductsToAttach): void
    {
        $cartProductsToAttach->map(fn(OrderProductData $product) => $product->getProductArray())->toArray();
        dd($cartProductsToAttach);
        $newOrder->orderProducts()->createMany($cartProductsToAttach);
    }
}

<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Http\Requests\OrderAddRequest;
use App\Http\Requests\OrdersRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceService
{
    public function __construct(
        private OrderDataService $orderDataService,
        private CartDataService $cartDataService,
    ) {}

    public function getOrder(int $orderId): OrderResource
    {
        $order = $this->orderDataService->getOrder($orderId);

        return new OrderResource($order);
    }

    public function getOrders(OrdersRequest $request): ResourceCollection
    {
        $filters = array_filter(
            $request->only([
                'userId',
                'productTitle',
                'minSum',
                'maxSum',
                'status',
                'createdAt',
            ])
        );
        $orders = $this->orderDataService->getFilteredOrders($filters);

        return new OrdersCollection($orders);
    }

    public function addOrder(OrderAddRequest $request, string $userId): void
    {
        $this->checkActionPermission('add', $userId);
        $requestOrderData = array_filter(
            $request->only([
                'name',
                'phone',
                'address',
            ])
        );
        $userCartProducts = $this->cartDataService
            ->setCartUser($userId)
            ->getCartProducts();
        $this->orderDataService->addNewOrder($requestOrderData, $userCartProducts);
    }

    public function updateOrder(string $orderId, OrderUpdateRequest $request): OrderResource
    {

    }

    private function checkActionPermission(string $resourceAction, string $userId): void
    {
        $authorizedUser = auth()->user();
        $orderUser = User::find($userId);

        if ($authorizedUser->cant($resourceAction, $orderUser)) {
            throw new ResourceAccessException();
        }
    }
}

<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function get(User $authorizedUser, Order $order): bool
    {
        return $this->isOwner($authorizedUser, $order);
    }

    public function index(User $authorizedUser, string $userId): bool
    {
        return $authorizedUser->id == $userId;
    }

    public function add(User $authorizedUser, string $userId): bool
    {
        return $authorizedUser->id == $userId;
    }

    private function isOwner(User $authorizedUser, Order $order): bool
    {
        return $authorizedUser->id === $order->user_id;
    }
}

<?php

namespace App\Policies;

use App\Exceptions\Resource\ResourceAccessException;
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

    public function add(User $authorizedUser, Order $order): bool
    {
        return $this->isOwner($authorizedUser, $order);
    }

    private function isOwner(User $authorizedUser, Order $order): bool
    {
        return $authorizedUser->id === $order->user_id;
    }

    protected function deny() {
        throw new ResourceAccessException();
    }
}

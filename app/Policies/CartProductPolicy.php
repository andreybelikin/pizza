<?php

namespace App\Policies;

use App\Models\User;

class CartProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function get(User $authorizedUser, string $userId): bool
    {
        return $this->isOwner($authorizedUser, $userId);
    }

    public function update(User $authorizedUser, string $userId): bool
    {
        return $this->isOwner($authorizedUser, $userId);
    }

    public function delete(User $authorizedUser, string $userId): bool
    {
        return $this->isOwner($authorizedUser, $userId);
    }

    private function isOwner(User $authorizedUser, string $userId): bool
    {
        return $authorizedUser->getKey() === (int)$userId;
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class CartProductPolicy
{
    /**
     * Create a new policy instance.
     */

    public function before(User $authorizedUser, string $ability): ?bool
    {
        return $authorizedUser->isAdmin() ?: null;
    }

    public function get(User $authorizedUser, string $userId): bool
    {
        return $this->isOwner($authorizedUser, $userId);
    }

    private function isOwner(User $authorizedUser, string $userId): bool
    {
        return $authorizedUser->getKey() === (int)$userId;
    }
}

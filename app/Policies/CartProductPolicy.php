<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartProductPolicy
{
    /**
     * Create a new policy instance.
     */
    use HandlesAuthorization;

    public function before(User $authorizedUser, string $ability): ?bool
    {
        return $authorizedUser->isAdmin() ? true : null;
    }

    public function get(User $authorizedUser, string $userId): bool
    {
        return $this->isOwner($authorizedUser, $userId) || $authorizedUser->isAdmin();
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

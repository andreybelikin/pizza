<?php

namespace App\Policies;

use App\Models\User;

class OrderPolicy
{
    public function update(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isAdmin($requestedUser) || $this->isOwner($authorizedUser, $requestedUser);
    }

    public function add(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isAdmin($requestedUser) || $this->isOwner($authorizedUser, $requestedUser);
    }

    private function isOwner(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isAdmin($requestedUser) || $this->isOwner($authorizedUser, $requestedUser);
    }

    private function isAdmin(User $authorizedUser): bool
    {
        return $authorizedUser->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class CartPolicy
{
    public function add(User $authorizedUser, User $cartUser): bool
    {
        return $this->isOwner($authorizedUser, $cartUser) || $this->isAdmin($authorizedUser);
    }

    public function get(User $authorizedUser, User $cartUser): bool
    {
        return $this->isOwner($authorizedUser, $cartUser) || $this->isAdmin($authorizedUser);
    }

    public function index(User $authorizedUser): bool
    {
        return $this->isAdmin($authorizedUser);
    }

    public function update(User $authorizedUser, User $cartUser): bool
    {
        return $this->isOwner($authorizedUser, $cartUser) || $this->isAdmin($authorizedUser);
    }

    public function delete(User $authorizedUser, User $cartUser): bool
    {
        return $this->isOwner($authorizedUser, $cartUser) || $this->isAdmin($authorizedUser);
    }

    private function isOwner(User $authorizedUser, User $cartUser): bool
    {
        return $authorizedUser->is($cartUser);
    }

    private function isAdmin(User $authorizedUser): bool
    {
        return $authorizedUser->isAdmin();
    }
}

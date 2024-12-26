<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    public function before(User $authorizedUser, string $ability): ?bool
    {
        return $authorizedUser->isAdmin() ?: null;
    }

    public function add(User $authorizedUser): bool
    {
        return $this->isAdmin($authorizedUser);
    }

    public function update(User $authorizedUser): bool
    {
        return $this->isAdmin($authorizedUser);
    }

    public function delete(User $authorizedUser): bool
    {
        return $this->isAdmin($authorizedUser);
    }

    private function isAdmin(User $authorizedUser): bool
    {
        return $authorizedUser->isAdmin();
    }
}

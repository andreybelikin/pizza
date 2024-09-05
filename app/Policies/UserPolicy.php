<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function get(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isOwner($authorizedUser, $requestedUser);
    }

    public function update(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isOwner($authorizedUser, $requestedUser);
    }

    public function delete(User $authorizedUser, User $requestedUser): bool
    {
        return $this->isOwner($authorizedUser, $requestedUser);
    }

    private function isOwner(User $authorizedUser, User $requestedUser): bool
    {
        return $authorizedUser->is($requestedUser);
    }
}

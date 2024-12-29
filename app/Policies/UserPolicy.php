<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $authorizedUser, string $ability)
    {
        return $authorizedUser->isAdmin() ? true : null;
    }

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
        return $authorizedUser->getKey() === $requestedUser->getKey();
    }
}

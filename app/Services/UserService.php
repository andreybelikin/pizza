<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UserService
{
    public function getUser(string $requestedUserId): ?User
    {
        $authorizedUser = auth()->user();
        $requestedUser = User::query()->find($requestedUserId);

        if ($authorizedUser->can('get', $requestedUser)) {
            return $requestedUser;
        }

        throw new AuthorizationException('Don\'t have permission to this resource');
    }
}

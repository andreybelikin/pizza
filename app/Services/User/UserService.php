<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserService
{
    public function getUser(string $requestedUserId): ?User
    {
        $authorizedUser = auth()->user()->getAuthIdentifier();
        $requestedUser = User::query()->find($requestedUserId);

        if (Gate::authorize('get', [$authorizedUser, $requestedUser])) {
            return $requestedUser;
        }

        return null;
    }
}

<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Models\User;

class UserDataService
{
    public function getUser(string $userId): User
    {
        $user = User::query()->find($userId);

        if (is_null($user)) {
            throw new ResourceAccessException();
        }

        return $user;
    }

    public function updateAddress(string $userId, string $address): void
    {
        !empty($address) ?: User::query()->find($userId)->address = $address;
    }
}

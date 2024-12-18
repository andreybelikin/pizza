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

    public function updateAddress(int $userId, ?string $address): void
    {
        !is_null($address) ?: User::query()->findOrFail($userId)->address = $address;
    }

    public function getDefaultAddress(int $userId): string
    {
        return User::query()->find($userId)->default_address;
    }
}

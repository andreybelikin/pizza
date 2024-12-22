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
        if (!is_null($address)) {
            $user = User::query()->findOrFail($userId);
            $user->address = $address;
            $user->save();
        }
    }

    public function getDefaultAddress(int $userId): string
    {
        return User::query()->find($userId)->default_address;
    }
}

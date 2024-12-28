<?php

namespace App\Services\Resource;

use App\Dto\Request\UpdateUserData;
use App\Models\User;

class UserDataService
{
    public function getUser(string $userId): User
    {
        return User::query()->findOrFail($userId);
    }

    public function updateUser(UpdateUserData $updateUserData): User
    {
        $user = User::query()->findOrFail($updateUserData->id);
        $user->update($updateUserData->getNewValues());
        $user->refresh();

        return $user;
    }

    public function deleteUser(string $userId): void
    {
        User::query()
            ->findOrFail($userId)
            ->delete();
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

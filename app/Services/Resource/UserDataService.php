<?php

namespace App\Services\Resource;

use App\Dto\Request\RegisterUserData;
use App\Dto\Request\UpdateUserData;
use App\Models\User;

class UserDataService
{
    public function getUser(string $userId): User
    {
        return User::query()->findOrFail($userId);
    }

    public function updateUser(User $user, UpdateUserData $updateUserData): User
    {
        $user->update($updateUserData->getNewValues());
        $user->refresh();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user::query()->delete();
    }

    public function updateAddress(int $userId, ?string $address): void
    {
        if (!is_null($address)) {
            $user = User::query()->findOrFail($userId);
            $user->address = $address;
            $user->save();
        }
    }

    public function createUser(RegisterUserData $registerUserData): User
    {
       return User::query()->create($registerUserData->toArray());
    }

    public function getDefaultAddress(int $userId): string
    {
        return User::query()->find($userId)->default_address;
    }
}

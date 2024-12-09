<?php

namespace App\Services\Resource;

use App\Models\User;

class UserDataService
{
    public function updateAddress(string $userId, string $address): void
    {
        User::query()->find($userId)->address = $address;
    }
}

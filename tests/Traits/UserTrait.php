<?php

namespace Tests\Traits;

use App\Models\User;

trait UserTrait
{
    public function createAdminAuthorizedUser(): void
    {
        $password = fake()->password();
        $user = User::factory()
            ->setPassword($password)
            ->create(['is_admin' => true]);
        AuthTrait::generateAccessToken($user->email, $password);
    }

    public function createUserWithCredentials(array $credentials): void
    {
        $credentials['password'] = bcrypt($credentials['password']);
        User::factory()->create($credentials);
    }

    public function getAnotherUser(): User
    {
        return User::query()
            ->skip(1)
            ->take(1)
            ->first();
    }

    public function getAdminUser(): User
    {
        $user = User::first();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function getUser(): User
    {
        return User::first();
    }

    public function createUser(): User
    {
        return User::factory()->create();
    }
}

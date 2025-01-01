<?php

namespace Tests\Traits;

use App\Models\User;

trait UserTrait
{
    use AuthTrait;

    public function createAdminAuthorizedUser(): void
    {
        $password = fake()->password();
        $user = User::factory()
            ->setPassword($password)
            ->create(['is_admin' => true]);
        $this->generateAccessToken($user->email, $password);
    }

    public function createUserWithCredentials(array $credentials): User
    {
        $credentials['password'] = bcrypt($credentials['password']);

        return User::factory()->create($credentials);
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

    public function getUserAddress(User $user): ?string
    {
        $user->refresh();

        return $user->address;
    }
}

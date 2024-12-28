<?php

namespace App\Services\Resource;

use App\Dto\Request\UpdateUserData;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UserResourceAdminService
{
    public function __construct(private UserDataService $userDataService) {}

    public function getUser(string $userId): JsonResource
    {
        Gate::authorize('get', [User::class, $userId]);
        $user = $this->userDataService->getUser($userId);

        return new UserResource($user);
    }

    public function updateUser(UserUpdateRequest $request, string $userId): JsonResource
    {
        Gate::authorize('update', [User::class, $userId]);
        $updateUserData = UpdateUserData::fromRequest($request);
        $updatedUser = $this->userDataService->updateUser($updateUserData);

        return new UserResource($updatedUser);
    }

    public function deleteUser(Request $request, string $userId): void
    {
        Gate::authorize('delete', [User::class, $userId]);
        $this->userDataService->deleteUser($userId);
    }
}

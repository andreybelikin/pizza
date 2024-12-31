<?php

namespace App\Services\Resource;

use App\Dto\Request\UpdateUserData;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\DBTransactionService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceAdminService
{
    public function __construct(
        private UserDataService $userDataService,
        private DBTransactionService $dbTransactionService
    ) {}

    public function getUser(string $userId): JsonResource
    {
        $user = $this->userDataService->getUser($userId);

        return new UserResource($user);
    }

    public function updateUser(UserUpdateRequest $request, string $userId): JsonResource
    {
        $user = $this->userDataService->getUser($userId);
        $updateUserData = UpdateUserData::fromRequest($request);
        $updatedUser = $this->dbTransactionService->execute(
            fn() => $this->userDataService->updateUser($user, $updateUserData)
        );

        return new UserResource($updatedUser);
    }

    public function deleteUser(string $userId): void
    {
        $user = $this->userDataService->getUser($userId);
        $this->dbTransactionService->execute(fn() => $this->userDataService->deleteUser($user));
    }
}

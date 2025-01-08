<?php

namespace App\Services\Resource;

use App\Dto\Request\UpdateUserData;
use App\Http\Requests\TokensRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\DBTransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UserResourceService
{
    public function __construct(
        private AuthService $authService,
        private UserDataService $userDataService,
        private DBTransactionService $dbTransactionService,
    ) {}

    public function getUser(string $userId): JsonResource
    {
        $user = $this->userDataService->getUser($userId);
        Gate::authorize('get', [User::class, $user]);

        return new UserResource($user);
    }

    public function updateUser(UserUpdateRequest $request, string $userId): JsonResource
    {
        $user = $this->userDataService->getUser($userId);
        Gate::authorize('update', [User::class, $user]);
        $updateUserData = UpdateUserData::fromRequest($request);
        $updatedUser = $this->dbTransactionService->execute(
            fn() => $this->userDataService->updateUser($user, $updateUserData)
        );

        return new UserResource($updatedUser);
    }

    public function deleteUser(Request $request, string $userId): void
    {
        $user = $this->userDataService->getUser($userId);
        Gate::authorize('delete', [User::class, $user]);
        $this->dbTransactionService->execute(
            fn() => $this->userDataService->deleteUser($user)
        );
        $this->authService->invalidateToken($request->bearerToken());
    }
}

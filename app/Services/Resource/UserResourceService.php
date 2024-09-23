<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\Auth\AuthService;

class UserResourceService
{
    public function __construct(
        private AuthService $authService,
        private CachedResourceService $cachedResourceService
    ) {}

    public function getUser(string $requestedUserId): User
    {
        $requestedUserResource = $this->getRequestedUser($requestedUserId);
        $this->checkActionPermission('get', $requestedUserResource);

        return $requestedUserResource;
    }

    public function updateUser(UserUpdateRequest $request): User
    {
        $requestedUserResource = $this->getRequestedUser($request->input('id'));
        $this->checkActionPermission('update', $requestedUserResource);

        $newData = array_filter(
            $request->only([
                'name',
                'surname',
                'phone',
                'email',
                'default_address',
            ])
        );

        $requestedUserResource->update($newData);
        $requestedUserResource->refresh();
        $this->cachedResourceService->update($requestedUserResource);

        return $requestedUserResource;
    }

    public function deleteUser(UserDeleteRequest $request): void
    {
        $requestedUserResource = $this->getRequestedUser($request->input('id'));
        $this->checkActionPermission('delete', $requestedUserResource);

        $requestedUserResource->delete();
        $this->cachedResourceService->delete('user', $request->input('id'));
        $this->authService->logoutUser($request);
    }

    private function getRequestedUser(string $userId): User
    {
        $requestedUser = $this->cachedResourceService->get('user', $userId);

        if (is_null($requestedUser)) {
            $requestedUser = User::query()->find($userId);
        }

        if (is_null($requestedUser)) {
            throw new ResourceNotFoundException();
        } else {
            $this->cachedResourceService->add($requestedUser);
        }

        return $requestedUser;
    }

    private function checkActionPermission(string $resourceAction, User $userResource): void
    {
        $authorizedUser = auth()->user();

        if ($authorizedUser->cant($resourceAction, $userResource)) {
            throw new ResourceAccessException();
        }
    }
}

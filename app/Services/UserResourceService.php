<?php

namespace App\Services;

use App\Exceptions\Resource\ResourceNotFoundException;
use App\Exceptions\Resource\ResourceAccessException;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;

class UserResourceService
{
    public function __construct(private AuthService $authService)
    {}

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

        return $requestedUserResource->refresh();
    }

    public function deleteUser(UserDeleteRequest $request): void
    {
        $requestedUserResource = $this->getRequestedUser($request->input('id'));
        $this->checkActionPermission('delete', $requestedUserResource);

        $requestedUserResource->delete();
        $this->authService->logoutUser($request);
    }

    private function getRequestedUser(string $userId): User
    {
        $requestedUser = User::query()->find($userId);

        if (is_null($requestedUser)) {
            throw new ResourceNotFoundException();
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

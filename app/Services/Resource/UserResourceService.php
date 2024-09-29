<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Resource\Abstract\ResourceServiceAbstract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceService extends ResourceServiceAbstract
{
    public function __construct(
        private AuthService $authService,
        private CachedResourceService $cachedResourceService
    ) {
        parent::__construct($this->cachedResourceService);
        parent::setResourceModel(User::class);
    }

    public function getUser(string $requestedUserId): JsonResource
    {
        /** @var User $requestedUserResource */
        $requestedUserResource = $this->getRequestedResource($requestedUserId);
        $this->checkActionPermission('get', $requestedUserResource);

        return new UserResource($requestedUserResource);
    }

    public function updateUser(UserUpdateRequest $request): JsonResource
    {
        $newData = $this->getNewData($request);
        /** @var User $requestedUserResource */
        $requestedUserResource = $this->getRequestedResource($request->input('id'));
        $this->checkActionPermission('update', $requestedUserResource);

        $this->updateResource($requestedUserResource, $newData);

        return new UserResource($requestedUserResource);
    }

    public function deleteUser(UserDeleteRequest $request): void
    {
        $userId = $request->input('id');
        /** @var User $requestedUserResource */
        $requestedUserResource = $this->getRequestedResource($userId);
        $this->checkActionPermission('delete', $requestedUserResource);
        $this->deleteResource($requestedUserResource);
        $this->invalidToken($request);
    }

    private function invalidToken(Request $request): void
    {
        $this->authService->logoutUser($request);
    }

    private function getNewData(UserUpdateRequest $request): array
    {
        return array_filter(
            $request->only([
                'name',
                'surname',
                'phone',
                'email',
                'default_address',
            ])
        );
    }

    private function checkActionPermission(string $resourceAction, User $userResource): void
    {
        $authorizedUser = auth()->user();

        if ($authorizedUser->cant($resourceAction, $userResource)) {
            throw new ResourceAccessException();
        }
    }
}

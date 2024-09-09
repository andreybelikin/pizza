<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    public function __construct(private readonly UserResourceService $userService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userService->getUser($userId);

        return response()
            ->json(new UserResource($user))
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $updatedUser = $this->userService->updateUser($request);

        return response()
            ->json(new UserResource($updatedUser))
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function delete(string $userId): JsonResponse
    {
        
    }
}

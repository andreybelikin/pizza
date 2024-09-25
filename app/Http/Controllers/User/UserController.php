<?php

namespace App\Http\Controllers\User;

use App\Dto\Response\Controller\User\UserDeletedResponseDto;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Services\Resource\UserResourceService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    public function __construct(private readonly UserResourceService $userService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userService->getUser($userId);

        return response()
            ->json($user)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $updatedUser = $this->userService->updateUser($request);

        return response()
            ->json($updatedUser)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function delete(UserDeleteRequest $request): JsonResponse
    {
        $this->userService->deleteUser($request);
        $deletedUserResponseDto = new UserDeletedResponseDto();

        return response()->json($deletedUserResponseDto->toArray(), $deletedUserResponseDto::STATUS);
    }
}

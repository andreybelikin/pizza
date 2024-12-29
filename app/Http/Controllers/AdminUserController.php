<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Services\Resource\UserResourceAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminUserController
{
    public function __construct(private readonly UserResourceAdminService $userResourceAdminService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userResourceAdminService->getUser($userId);

        return response()
            ->json($user)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(UserUpdateRequest $request, string $userId): JsonResponse
    {
        $updatedUser = $this->userResourceAdminService->updateUser($request, $userId);

        return response()
            ->json($updatedUser)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $userId): Response
    {
        $this->userResourceAdminService->deleteUser($userId);

        return response('');
    }
}

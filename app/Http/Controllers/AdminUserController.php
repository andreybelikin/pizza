<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Services\Resource\UserAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminUserController
{
    public function __construct(private readonly UserAdminService $userAdminService)
    {}

    public function show(string $userId): JsonResponse
    {
        $user = $this->userAdminService->getUser($userId);

        return response()
            ->json($user)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(UserUpdateRequest $request, string $userId): JsonResponse
    {
        $updatedUser = $this->userAdminService->updateUser($request, $userId);

        return response()
            ->json($updatedUser)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function destroy(string $userId): Response
    {
        $this->userAdminService->deleteUser($userId);

        return response('');
    }
}

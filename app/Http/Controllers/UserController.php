<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Services\Resource\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    public function __construct(private readonly UserService $userService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userService->getUser($userId);

        return response()
            ->json($user)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function update(UserUpdateRequest $request, $userId): JsonResponse
    {
        $updatedUser = $this->userService->updateUser($request, $userId);

        return response()
            ->json($updatedUser)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function delete(Request $request, string $userId): Response
    {
        $this->userService->deleteUser($request, $userId);

        return response('');
    }
}

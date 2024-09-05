<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserResource;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    public function __construct(private readonly UserService $userService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userService->getUser($userId);

        return response()
            ->json(new UserResource($user))
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
        ;
    }

    public function update(string $userId): JsonResponse
    {

    }

    public function delete(string $userId): JsonResponse
    {

    }
}

<?php

namespace App\Http\Controllers\User;

use App\Services\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class UserController
{
    public function __construct(private UserService $userService)
    {}

    public function get(string $userId): JsonResponse
    {
        $user = $this->userService->getUser($userId);

        if (is_null($user)) {
            // Вернуть json resource response?
        }
    }

    public function update(string $userId): JsonResponse
    {

    }

    public function delete(string $userId): JsonResponse
    {

    }
}

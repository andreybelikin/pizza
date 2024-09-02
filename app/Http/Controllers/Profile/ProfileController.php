<?php

namespace App\Http\Controllers\Profile;

use Symfony\Component\HttpFoundation\JsonResponse;

abstract class ProfileController
{
    public function update(string $id): JsonResponse
    {
        
    }

    public function add(): JsonResponse
    {

    }

    public function delete(string $id): JsonResponse
    {

    }
}

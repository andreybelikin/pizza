<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class UserController
{
    public function show(string $id): User
    {

    }
}

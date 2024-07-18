<?php

namespace App\Services\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthService
{
    public function saveNewUser(RegisterRequest $request): string
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->password_hash = bcrypt($request->input('password'));
        $user->is_admin = false;

        return auth()->login($user);
    }
}

<?php

namespace App\Services\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthService
{
    public function saveNewUser(RegisterRequest $request): string
    {
        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password_hash' => bcrypt($request->input('password')),
            'default_address' => $request->input('default_address'),
            'is_admin' => false,
        ]);
        $user->save();

        return auth()->login($user);
    }
}

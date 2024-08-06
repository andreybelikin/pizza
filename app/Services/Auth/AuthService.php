<?php

namespace App\Services\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function saveNewUser(RegisterRequest $request): string
    {
        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
            'default_address' => $request->input('default_address'),
            'is_admin' => false,
        ]);
        $user->save();

        return auth()->login($user);
    }

    public function authenticateUser(AuthenticateRequest $request): ?string
    {
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        if (!$token) {
            throw new InvalidCredentialsException(
                'Invalid email or password',
                Response::HTTP_BAD_REQUEST
            );
        }

        return $token;
    }

    public function logoutUser(): void
    {
        auth()->logout();
    }
}

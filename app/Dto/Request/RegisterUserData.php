<?php

namespace App\Dto\Request;

use App\Http\Requests\Auth\RegisterRequest;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $surname,
        public string $email,
        public int $phone,
        public string $password,
        public string $defaultAddress,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->input('name'),
            $request->input('surname'),
            $request->input('email'),
            (int)$request->input('phone'),
            $request->input('password'),
            $request->input('default_address'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'default_address' => $this->defaultAddress,
        ];
    }
}

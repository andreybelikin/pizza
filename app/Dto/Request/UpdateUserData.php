<?php

namespace App\Dto\Request;

use App\Http\Requests\User\UserUpdateRequest;

readonly class UpdateUserData
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $surname,
        public ?int $phone,
        public ?string $email,
        public ?string $defaultAddress,
    ) {}

    public static function fromRequest(UserUpdateRequest $request): self
    {
        return new self(
            id: $request->route('userId'),
            name: $request->get('name') ?? null,
            surname: $request->get('surname') ?? null,
            phone: (int)$request->get('phone') ? (int)$request->get('phone') : null,
            email: $request->get('email') ?? null,
            defaultAddress: $request->get('defaultAddress') ?? null
        );
    }

    public function getNewValues(): array
    {
        return array_filter([
            'name' => $this->name,
            'surname' => $this->surname,
            'phone' => $this->phone,
            'email' => $this->email,
            'default_address' => $this->defaultAddress,
        ]);
    }
}

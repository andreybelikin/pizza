<?php

namespace App\Enums\Limit\Cart;

enum CartProductLimit: int
{
    case Pizza = 10;
    case Drink = 20;

    public function getName(): string
    {
        return lcfirst($this->name);
    }

    public static function getTypes(): array
    {
        return array_map(fn ($case) => lcfirst($case->name), self::cases());
    }
}

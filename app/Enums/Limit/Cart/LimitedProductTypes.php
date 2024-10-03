<?php

namespace App\Enums\Limit\Cart;

enum LimitedProductTypes: int
{
    case Pizza = 10;
    case Drink = 20;

    public static function getLimit(string $name): ?int
    {
        $UcName = ucfirst($name);

        foreach (self::cases() as $case) {
            if ($case->name === $UcName) {
                return $case->value;
            }
        }

        return null;
    }

    public static function getTypes(): array
    {
        return array_map(fn ($case) => lcfirst($case->name), self::cases());
    }
}

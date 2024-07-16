<?php

namespace App\Enums\Restriction;

enum ProductTypeRestriction: int
{
    case Pizza = 10;
    case Drink = 20;

    public static function getRestrictedTypeNames(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function getRestrictionCompliance(string $type, int $quantity): int
    {
        $restriction = array_filter(
            self::cases(),
            fn($case) => $case->name === $type
        );

        return max(0, $restriction->value - $quantity);
    }
}

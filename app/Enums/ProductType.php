<?php

namespace App\Enums;

enum ProductType: string
{
    case Pizza = 'pizza';
    case Drink = 'drink';
    case Fries = 'fries';
    case Chips = 'chips';

    public static function getTypes(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}

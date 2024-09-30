<?php

namespace App\Enums;

use App\Enums\Restriction\ProductTypeRestriction;

enum ProductType: string
{
    case Pizza = 'pizza';
    case Drink = 'drink';
    case Fries = 'fries';
    case Chips = 'chips';
    public static function getTypes(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function getUnrestrictedTypes(): array
    {
        $restrictedTypes = ProductTypeRestriction::getRestrictedTypeNames();

        return array_filter(self::cases(), fn($case) => !in_array($case->name, $restrictedTypes));
    }
}

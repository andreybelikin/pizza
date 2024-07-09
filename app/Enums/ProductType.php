<?php

namespace App\Enums;

use App\Enums\Restriction\ProductTypeRestriction;

enum ProductType
{
    case Pizza;
    case Drink;
    case Fries;
    case Chips;
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

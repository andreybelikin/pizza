<?php

namespace App\Enums\Limit\Cart;

enum ProductTypeLimit: int
{
    case Pizza = 10;
    case Drink = 20;

    public function getLimitByTypeName(string $name): ?int
    {
        $UcName = ucfirst($name);

        foreach (self::cases() as $case) {
            if ($case->name === $UcName) {
                return $case->value;
            }
        }

        return null;
    }
}

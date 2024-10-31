<?php

namespace App\Enums;
enum OrderStatus: string
{
    case DELIVERED = 'delivered';
    case CREATED = 'created';

    public static function getStatuses(): array
    {
        return array_map(fn ($status) => $status->value, self::cases());
    }
}

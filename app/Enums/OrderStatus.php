<?php

namespace App\Enums;
enum OrderStatus: string
{
    case Delivered = 'delivered';
    case Created = 'created';

    public static function getStatuses(): array
    {
        return array_map(fn ($status) => $status->value, self::cases());
    }
}

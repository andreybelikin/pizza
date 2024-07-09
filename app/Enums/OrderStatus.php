<?php

namespace App\Enums;
enum OrderStatus
{
    case DELIVERED;
    case CREATED;
    public static function getOrderStatuses(): array
    {
        return [
            self::CREATED,
            self::DELIVERED,
        ];
    }
}

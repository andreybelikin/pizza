<?php

namespace App\Dto\Request;

readonly class GetCartData
{
    public int $userId;

    public function __construct(string $userId)
    {
        $this->userId = (int)$userId;
    }
}

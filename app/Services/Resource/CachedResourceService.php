<?php

namespace App\Services\Resource;

use Illuminate\Database\Eloquent\Model;

class CachedResourceService
{
    private function getKey(Model $resourceModel): string
    {
        $cacheMapping = [
            'User' => 'user',
            'Product' => 'product',
            'Order' => 'order',
        ];

        return $cacheMapping[$resourceModel::class];
    }

    public function add(Model $resourceModel): void
    {
        $key = $this->getKey($resourceModel);

        
    }
}

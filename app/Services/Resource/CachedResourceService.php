<?php

namespace App\Services\Resource;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CachedResourceService
{
    private const MODEL_RESOURCE_TYPE_MAPPING = [
        User::class => 'user',
        Product::class => 'product',
        Order::class => 'order',
    ];

    private const RESOURCE_TYPE_MODEL_MAPPING = [
        'user' => User::class,
        'product' => Product::class,
        'order' => Order::class,
    ];

    public function add(Model $resourceModel): void
    {
        $resourceType = self::MODEL_RESOURCE_TYPE_MAPPING[$resourceModel::class];
        $resourceEntryKey = sprintf('%s:%s', $resourceType, $resourceModel->getKey());

        Cache::add($resourceEntryKey, serialize($resourceModel));
    }

    public function get(string $resourceType, string $resourceId): ?Model
    {
        $resourceEntryKey = sprintf('%s:%s', $resourceType, $resourceId);
        $resourceData = Cache::get($resourceEntryKey);

        if (empty($resourceData)) {
            return null;
        }

        return unserialize($resourceData);
    }

    public function update(Model $resourceModel): void
    {
        $this->add($resourceModel);
    }

    public function delete(string $resourceType, string $resourceId): void
    {
        $resourceEntryKey = sprintf('%s:%s', $resourceType, $resourceId);
        Cache::delete($resourceEntryKey);
    }
}

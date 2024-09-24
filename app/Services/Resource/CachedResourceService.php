<?php

namespace App\Services\Resource;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @method void addUser(Model $resourceModel)
 * @method Model|null getUser(string $resourceId)
 * @method void updateUser(Model $resourceModel)
 * @method void deleteUser(string $resourceId)
 * @method void addOrder(Model $resourceModel)
 * @method Model|null getOrder(string $resourceId)
 * @method void updateOrder(Model $resourceModel)
 * @method void deleteOrder(string $resourceId)
 * @method void addProduct(Model $resourceModel)
 * @method Model|null getProduct(string $resourceId)
 * @method void updateProduct(Model $resourceModel)
 * @method void deleteProduct(string $resourceId)
 */
class CachedResourceService
{
    public function __call(string $name, array $arguments): void
    {
        [$methodAction, $resourceName] = $this->getCalledMethod($name);

        $resourceId = $arguments[0] instanceof Model ? $arguments[0]->getKey() : $arguments[0];
        $entryPrefixKey = sprintf('%s:%s', strtolower($resourceName), $resourceId);

        $resourceData = $arguments[0] instanceof Model ?: serialize($arguments[0]->toArray());
        $resourceModelClass = $arguments[0] instanceof Model ?: serialize($arguments[0]->getMorphClass());

        match ($methodAction) {
            'add', 'update' => $this->{$methodAction}($entryPrefixKey, $resourceData),
            'get' => $this->{$methodAction}($entryPrefixKey, $resourceId, $resourceModelClass),
            'delete' => $this->{$methodAction}($entryPrefixKey)
        };
    }

    public function add(string $entryPrefixKey, array $resourceData): void
    {
        Cache::add($entryPrefixKey, $resourceData);
    }

    public function get(string $entryPrefixKey, string $resourceId, string $modelClass): ?Model
    {
        $resourceData = unserialize(Cache::get($entryPrefixKey));

        if (is_null($resourceData)) {
            return null;
        }

        /** @var Model $resourceModel */
        $resourceModel = new $modelClass();
        $resourceModel->setAttribute('id', $resourceId);
        $resourceModel->exists = true;
        $resourceModel->fill($resourceData);

        return $resourceModel;
    }

    public function update(string $entryPrefixKey, array $resourceData): void
    {
        Cache::put($entryPrefixKey, $resourceData);
    }

    public function delete(string $entryPrefixKey): void
    {
        Cache::delete($entryPrefixKey);
    }

    private function getCalledMethod(string $name): array
    {
        return preg_split(
            '/(get|add|update|delete)/',
            $name,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
    }
}

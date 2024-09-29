<?php

namespace App\Services\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class CachedResourceService
{
    private string $modelClass;
    private string $resourceName;

    public function setResourceData(string $className): void
    {
        $this->modelClass = $className;
        $name = $this->getResourceName($className);
        $this->resourceName = strtolower($name);
    }

    public function add(array $resourceData): void
    {
        $entryPrefixKey = $this->getEntryPrefixKey($resourceData['id']);
        Cache::add($entryPrefixKey, serialize($resourceData));
    }

    public function get(string $resourceId): ?Model
    {
        $entryPrefixKey = $this->getEntryPrefixKey($resourceId);
        $resourceData = unserialize(Cache::get($entryPrefixKey));

        if (is_null($resourceData)) {
            return null;
        }

        return $this->getModel($resourceId, $resourceData);
    }

    public function update(array $resourceData): void
    {
        $entryPrefixKey = $this->getEntryPrefixKey($resourceData['id']);
        Cache::put($entryPrefixKey, serialize($resourceData));
    }

    public function delete(string $resourceId): void
    {
        $entryPrefixKey = $this->getEntryPrefixKey($resourceId);
        Cache::delete($entryPrefixKey);
    }

    private function getResourceName(string $resourceModelClass): string
    {
        $name = (new ReflectionClass($resourceModelClass))->getShortName();

        return strtolower($name);
    }

    private function getEntryPrefixKey(string $resourceId): string
    {
        return sprintf('%s:%s', strtolower($this->resourceName), $resourceId);
    }

    private function getModel(string $resourceId, array $resourceData): Model
    {
        /** @var Model $resourceModel */
        $resourceModel = new $this->modelClass();
        $resourceModel->setAttribute('id', $resourceId);
        $resourceModel->exists = true;
        $resourceModel->fill($resourceData);

        return $resourceModel;
    }
}

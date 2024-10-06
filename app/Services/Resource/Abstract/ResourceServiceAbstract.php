<?php

namespace App\Services\Resource\Abstract;

use App\Exceptions\Resource\ResourceNotFoundException;
use App\Services\Resource\CachedResourceService;
use Illuminate\Database\Eloquent\Model;

abstract class ResourceServiceAbstract
{
    private string $modelClass;
    private CachedResourceService $cachedResourceService;

    public function __construct()
    {
        $this->cachedResourceService = new CachedResourceService();
    }

    public function setResourceModel(string $className): void
    {
        $this->modelClass = $className;
        $this->cachedResourceService->setResourceData($className);
    }

    public function getRequestedResource(string $resourceId): Model
    {
        $requestedResource = $this->getFromCache($resourceId);

        if (is_null($requestedResource)) {
            $requestedResource = $this->getFromDB($resourceId);
        }

        $this->ensureResourceFound($requestedResource);
        $this->cacheIfNeeded($requestedResource);

        return $requestedResource;
    }

    public function updateResource(Model $resource, array $newData): void
    {
        $this->updateInDB($resource, $newData);
        $this->updateInCache($resource);
    }

    public function deleteResource(Model $resource): void
    {
        $this->deleteFromDB($resource);
        $this->deleteFromCache($resource->getKey());
    }

    private function getFromDB(string $resourceId): ?Model
    {
        /** @var Model $modelClass */
        $modelClass = $this->modelClass;

        return $modelClass::query()->find($resourceId);
    }

    private function updateInDB(Model $resource, array $newData): void
    {
        $resource->update($newData);
        $resource->refresh();
    }

    private function updateInCache(Model $resource): void
    {
        $this->cachedResourceService->update($resource->toArray());
    }

    private function deleteFromDB(Model $resource): void
    {
        $resource->delete();
    }

    private function getFromCache(string $resourceId): ?Model
    {
        $requestedResource = $this->cachedResourceService->get($resourceId);

        return $requestedResource;
    }

    private function deleteFromCache(string $resourceId): void
    {
        $this->cachedResourceService->delete($resourceId);
    }

    private function ensureResourceFound(?Model $resource): void
    {
        if (is_null($resource)) {
            throw new ResourceNotFoundException();
        }
    }

    private function cacheIfNeeded(Model $requestedModel): void
    {
        $this->cachedResourceService->add($requestedModel->toArray());
    }
}

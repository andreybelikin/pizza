<?php

namespace App\Services\Resource;

use App\Exceptions\Resource\ResourceAccessException;
use App\Exceptions\Resource\ResourceNotFoundException;
use App\Http\Requests\Product\ProductAddRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResourceService
{
    public function getProduct(string $id): JsonResource
    {
        $product = Product::query()->find($id);

        if (is_null($product)) {
            throw new ResourceNotFoundException();
        }

        return new ProductResource($product);
    }

    public function getProducts(Request $request): ResourceCollection
    {
        return new ProductsCollection(Product::paginate(15));
    }

    public function addProduct(ProductAddRequest $request): void
    {
        $productData = $request->only([
            'title',
            'description',
            'type',
            'price',
        ]);

        $this->checkActionPermission('add');

        $newProduct = new Product($productData);
        $newProduct->save();
    }

    public function updateProduct(ProductUpdateRequest $request): JsonResource
    {
        $requestedProduct = Product::query()->find($request->input('id'));

        if (is_null($requestedProduct)) {
            throw new ResourceNotFoundException();
        }

        $this->checkActionPermission('update');

        $newData = array_filter(
            $request->only([
                'title',
                'description',
                'type',
                'price',
            ])
        );
        $requestedProduct->update($newData);

        return $requestedProduct->refresh();
    }

    public function delete(Request $request)
    {

    }

    private function checkActionPermission(string $resourceAction): void
    {
        $authorizedUser = auth()->user();

        if ($authorizedUser->cant($resourceAction, Product::class)) {
            throw new ResourceAccessException();
        }
    }

}

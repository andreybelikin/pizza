<?php

namespace App\Services\Cart;

use App\Enums\ProductType;
use App\Enums\Restriction\ProductTypeRestriction;
use App\Models\Product;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private Authenticatable $user;

    public function createItems(Request $request): array
    {
        $this->user = Auth::user();
        $result = [
            'unrestrictedCreatedProducts' => true,
            'createdRestrictedProducts' => [],
            'violatedRestrictedTypes' => [],
        ];

        $requestProducts = $this->getRequestProducts($request);

        $result = $this->createUnrestrictedProducts($requestProducts, $result);
        $result = $this->createRestrictedProducts($requestProducts, $result);

        return $this->getCreateResult($result);
    }

    private function handleRestrictedProducts(array $requestProducts, array $result): array
    {
        $cartProducts = $this->getCartProducts();

        foreach (ProductTypeRestriction::getRestrictedTypeNames() as $restrictedType) {
            $restrictionCompliance = $this->getRestrictionCompliance($cartProducts, $restrictedType);
            $productsByType = $requestProducts->whereIn('type', [$restrictedType]);
            $productsQuantityByType = $productsByType->sum('quantity');

            if ($restrictionCompliance > 0 && $productsQuantityByType <= $restrictionCompliance) {
                $result['createdRestrictedProducts'][] = $productsByType->pluck('id');
            } else {
                $result['violatedRestrictedTypes'][] = $restrictedType;
            }
        }

        return $result;
    }

    private function getRestrictionCompliance(array $products, string $restrictedType): int
    {
        $productsByType = $products
            ->whereIn('type', [$restrictedType])
            ->count()
        ;

        return ProductTypeRestriction::getRestrictionCompliance($restrictedType, $productsByType);
    }
    private function getProducts(array $productIds): array
    {
        return Product::query()->find($productIds, ['id', 'type']);
    }

    private function getRequestProducts(Request $request): array
    {
        $requestProductIds = array_column(
            $request->input('products'),
            'id')
        ;

        return $this->getProducts($requestProductIds);
    }

    private function getCartProducts(): array
    {
        $cartProductIds = $this->user->products()->pluck(['product_id']);

        return $this->getProducts($cartProductIds);
    }

    private function createUnrestrictedProducts(array $requestProducts, array $result): array
    {
        $unrestrictedTypes = ProductType::getUnrestrictedTypes();
        $unrestrictedProducts = $requestProducts->whereIn('type', $unrestrictedTypes);

        if (!empty($unrestrictedProducts)) {
            foreach ($unrestrictedProducts as $product) {
                for ($i = 1; $i < $product['quantity']; $i++) {
                    $this->user->products()->attach($unrestrictedProducts);
                }
            }
            $result['unrestrictedCreatedProducts'] = true;
        } else {
            $result['unrestrictedCreatedProducts'] = false;
        }

        return $result;
    }

    private function createRestrictedProducts(array $requestProducts, array $result): array
    {
        $cartProducts = $this->getCartProducts();

        foreach (ProductTypeRestriction::getRestrictedTypeNames() as $restrictedType) {
            $restrictionCompliance = $this->getRestrictionCompliance($cartProducts, $restrictedType);
            $productsByType = $requestProducts->whereIn('type', [$restrictedType]);
            $productsQuantityByType = $productsByType->sum('quantity');

            if ($restrictionCompliance > 0 && $productsQuantityByType <= $restrictionCompliance) {
                $result['productsFitRestrictions'][] = $productsByType->pluck('id');
            } else {
                $result['violatedRestrictedTypes'][] = $restrictedType;
            }
        }

        if (!empty($result['createdRestrictedProducts'])) {
            $this->user->products()->syncWithoutDetaching($result['createdRestrictedProducts']);
        }

        return $result;
    }

    private function getCreateResult(array $result): array
    {
        if ($result['unrestrictedProducts']) {

        }
        $createdResult = match ($result['unrestrictedProducts']) {
            empty($result['violatedRestrictedTypes']) => ['message' => 'OK'],
            !empty($result['violatedRestrictedTypes']) => [
                'message' => 'Partially added',
                'declinedTypes' => array_map(function($type) {
                    return [
                        'type' => ProductTypeRestriction::{$type},
                        'restriction' => ProductTypeRestriction::{$type}->value,
                    ];
                }, $result['violatedRestrictedTypes']),
            ]
        };
        $createdResult = match ($result['unrestrictedProducts']) {
            empty($result['violatedRestrictedTypes']) => ['message' => 'OK'],
            !empty($result['violatedRestrictedTypes']) => [
                'message' => 'Partially added',
                'declinedTypes' => array_map(function($type) {
                    return [
                        'type' => ProductTypeRestriction::{$type},
                        'restriction' => ProductTypeRestriction::{$type}->value,
                    ];
                }, $result['violatedRestrictedTypes']),
            ]
        };
    }
}

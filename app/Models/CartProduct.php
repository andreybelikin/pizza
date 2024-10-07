<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CartProduct extends Model
{
    protected $table = 'cart_product';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public static function getCartDistinctProducts(int $cartUserId): array
    {
        return self::query()
            ->select([DB::raw('product_id as id'), DB::raw('COUNT(*) as quantity')])
            ->where('user_id', '=', $cartUserId)
            ->groupBy(['product_id'])
            ->get()
            ->toArray()
        ;
    }

    public static function addProductsToCart(array $products): void
    {
        self::query()->insert($products);
    }

    public static function deleteCartProducts(array $productsIds, int $userId): void
    {
        self::query()
            ->where('user_id', '=', $userId)
            ->whereIn('product_id', $productsIds)
            ->delete()
        ;
    }

    public static function emptyCart(int $cartUserId): void
    {
        self::query()
            ->where('user_id', '=', $cartUserId)
            ->delete()
        ;
    }

    public static function getCart(string $cartUserId): array
    {
        $cart = self::getCartDistinctProducts($cartUserId);

        if (!empty($cart)) {
            array_walk($cart,
                function ($cartRecord) use ($cartUserId) {
                    /** @var Product $cartProduct */
                    $cartProduct = self::find($cartRecord['id'])
                        ->product()
                        ->get()
                    ;
                    $cartRecord['title'] = $cartProduct->title;
                    $cartRecord['price'] = $cartProduct->price;
                }
            );
        }

        return $cart;
    }
}

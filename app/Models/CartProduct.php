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

    public static function getCartDistinctProducts(): array
    {
        return self::query()
            ->select([DB::raw('product_id as id'), DB::raw('COUNT(*) as quantity')])
            ->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->groupBy(['product_id'])
            ->get()
            ->toArray()
        ;
    }

    public static function addProductsToCart(array $products): void
    {
        self::query()->insert($products);
    }

    public static function deleteCartProduct(int $productId, int $limit = 0): void
    {
        self::query()
            ->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->where('product_id', $productId)
            ->limit($limit)
            ->delete()
        ;
    }

    public static function emptyCart(): void
    {
        self::query()
            ->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->delete()
        ;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'price',
    ];
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cart_product');
    }
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when(($filters['title']) ?? null, fn ($q, $title) => $q->where('title', 'like', "%{$title}%"));

        $query->when(
            !empty($filters['description']),
            fn ($q, $description) => $q->where('description', 'like', "%{$description}%")
        );

        $query->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', '=', $type));

        $query->when(
            $filters['minPrice'] ?? null,
            fn ($q, $minPrice) => $q->where('price', '>=', (float) $minPrice)
        );

        $query->when(
            $filters['maxPrice'] ?? null,
            fn ($q, $maxPrice) => $q->where('price', '<=', (float) $maxPrice)
        );

        return $query;
    }

    public static function getProductsTypes(array $ids, array $types): array
    {
        return self::query()
            ->select(['id', 'type'])
            ->whereIn('id', $ids)
            ->whereIn('type', $types)
            ->get()
            ->toArray()
        ;
    }
}

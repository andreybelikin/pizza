<?php

namespace App\Models;

use App\Dto\Request\ListProductFilterData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsToMany(User::class, 'cart_product', 'product_id', 'user_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }

    public function scopeFilter(Builder $query, ListProductFilterData $filters): Builder
    {
        $query->when($filters->title, fn ($q, $title) => $q->where('title', 'like', "%{$title}%"));

        $query->when(
            $filters->description,
            fn ($q, $description) => $q->where('description', 'like', "%{$description}%")
        );

        $query->when($filters->type, fn ($q, $type) => $q->where('type', '=', $type));

        $query->when(
            $filters->minPrice,
            fn ($q, $minPrice) => $q->where('price', '>=', $minPrice)
        );

        $query->when(
            $filters->maxPrice,
            fn ($q, $maxPrice) => $q->where('price', '<=', $maxPrice)
        );

        return $query;
    }
}

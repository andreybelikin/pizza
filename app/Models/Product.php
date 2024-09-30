<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;

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
        $query->when(!empty($filters['title']), fn ($q, $title) => $q->where('price', 'like', `%{$title}%`));

        $query->when(!empty($filters['type']), fn ($q, $type) => $q->where('type', '=', $type));

        $query->when(!empty($filters['minPrice']), fn ($q, $minPrice) => $q->where('price', '>=', $minPrice));

        $query->when(!empty($filters['maxPrice']), fn ($q, $maxPrice) => $q->where('price', '<=', $maxPrice));

        return $query;
    }
}

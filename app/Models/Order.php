<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        'address',
        'total',
        'phone',
        'name',
    ];

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when(($filters['userId']) ?? null, fn ($q, $userId) => $q->where('user_id', '=', $userId));

        $query->when(
            !empty($filters['productTitle']),
            function ($query) use ($filters){
                $productTitle = $filters['productTitle'];
                $query->whereHas(
                    'orderProducts',
                    fn (Builder $query) => $query->where('title', 'like', '%' . $productTitle .'%')
                );
            }
        );

        $query->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', '=', $status));

        $query->when(
            $filters['minSum'] ?? null,
            fn ($q, $minSum) => $q->where('minSum', '>=', (float) $minSum)
        );

        $query->when(
            $filters['maxSum'] ?? null,
            fn ($q, $maxSum) => $q->where('maxSum', '<=', (float) $maxSum)
        );

        return $query;
    }
}

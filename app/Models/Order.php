<?php

namespace App\Models;

use App\Dto\Request\ListOrderFilterData;
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
        'user_id',
    ];

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, ListOrderFilterData $filters): Builder
    {
        $query->when($filters->userId, fn ($q, $userId) => $q->where('user_id', '=', $userId));

        $query->when(
            $filters->createdAt,
            function ($q, $createdAt) {
                $q->whereDate('created_at', '=', $createdAt);
            }
        );

        $query->when(
            $filters->productTitle,
            function ($query, $productTitle) use ($filters) {
                $query->whereHas(
                    'orderProducts',
                    fn (Builder $query) => $query->where('title', 'like', '%' . $productTitle . '%')
                );
            }
        );

        $query->when($filters->status, fn ($q, $status) => $q->where('status', '=', $status));

        $query->when($filters->minTotal, fn ($q, $minSum) => $q->where('total', '>=', $minSum));

        $query->when($filters->maxTotal, fn ($q, $maxSum) => $q->where('total', '<=', $maxSum));

        return $query;
    }

    public function updateTotal(): void
    {
        $this->total = $this->orderProducts()
            ->get()
            ->sum(fn (OrderProduct $product) => $product->price * $product->quantity);
        $this->save();
    }
}

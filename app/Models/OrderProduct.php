<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'quantity',
        'price',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected static function booted()
    {
        static::updated(function (OrderProduct $orderProduct) {
            if ($orderProduct->isDirty(['quantity', 'price'])) {
                $orderProduct->order->updateTotal();
            }
        });

        static::created(function (OrderProduct $orderProduct) {
            $orderProduct->order->updateTotal();
        });
    }
}

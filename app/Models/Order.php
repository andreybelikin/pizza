<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

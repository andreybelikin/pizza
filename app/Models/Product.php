<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
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
}

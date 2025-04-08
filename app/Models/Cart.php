<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'open'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productsWithQuantity()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price')
            ->wherePivot('quantity', '>', 0);
    }
}

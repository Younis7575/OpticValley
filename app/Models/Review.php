<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'name', 'rating', 'title', 'body', 'is_approved'];

    protected $casts = ['is_approved' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(fn (Review $r) => $r->product?->recalculateRating());
        static::deleted(fn (Review $r) => $r->product?->recalculateRating());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

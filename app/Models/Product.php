<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'price', 'sale_price', 'stock', 'low_stock_threshold', 'gender', 'frame_shape', 'frame_type', 'frame_material',
        'frame_color', 'frame_size', 'lens_info', 'specifications', 'prescription_enabled', 'is_featured',
        'is_best_seller', 'is_new', 'is_active', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'specifications' => 'array',
        'prescription_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new' => 'boolean',
        'is_active' => 'boolean',
        'rating_avg' => 'float',
    ];

    public const GENDERS = ['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids', 'unisex' => 'Unisex'];

    public const FRAME_TYPES = ['full-rim' => 'Full Frame', 'half-rim' => 'Half Frame', 'rimless' => 'Rimless Frame'];

    public const SHAPES = ['round', 'rectangle', 'square', 'aviator', 'cat-eye', 'oval', 'wayfarer', 'geometric'];

    public const MATERIALS = ['acetate', 'metal', 'titanium', 'tr90', 'stainless steel', 'mixed'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeInCategory(Builder $q, Category $category): Builder
    {
        $ids = $category->descendantIds();

        return $q->where(fn ($w) => $w->whereIn('category_id', $ids)
            ->orWhereHas('categories', fn ($c) => $c->whereIn('categories.id', $ids)));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->hasDiscount() ? $this->sale_price : $this->price);
    }

    public function hasDiscount(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        return $this->hasDiscount() ? (int) round((1 - $this->sale_price / $this->price) * 100) : 0;
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }

    public function getImageUrlAttribute(): ?string
    {
        $img = $this->relationLoaded('images') ? $this->images->first() : $this->primaryImage;

        return $img?->url;
    }

    public function recalculateRating(): void
    {
        $stats = $this->reviews()->where('is_approved', true)->selectRaw('AVG(rating) as avg, COUNT(*) as cnt')->first();
        $this->forceFill(['rating_avg' => round((float) $stats->avg, 2), 'rating_count' => (int) $stats->cnt])->saveQuietly();
    }
}

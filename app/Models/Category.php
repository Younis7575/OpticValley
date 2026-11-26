<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasImageUrl;

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'image', 'is_active', 'show_in_menu', 'sort_order', 'meta_title', 'meta_description'];

    protected $casts = ['is_active' => 'boolean', 'show_in_menu' => 'boolean'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /** IDs of this category and all of its descendants. */
    public function descendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children()->get() as $child) {
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

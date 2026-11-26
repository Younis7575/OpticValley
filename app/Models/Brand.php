<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'description', 'is_featured', 'is_active'];

    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Media::url($this->logo) : null;
    }
}

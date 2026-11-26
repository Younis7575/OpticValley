<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['position', 'eyebrow', 'title', 'subtitle', 'image', 'button_text', 'link', 'theme', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const POSITIONS = ['hero' => 'Homepage hero slider', 'promo' => 'Promotional tiles', 'strip' => 'Announcement strip'];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Media::url($this->image) : null;
    }
}

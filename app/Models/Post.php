<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'excerpt', 'body', 'image', 'meta_title', 'meta_description', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Media::url($this->image) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

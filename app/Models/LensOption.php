<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LensOption extends Model
{
    protected $fillable = ['name', 'description', 'price', 'requires_prescription', 'is_active', 'sort_order'];

    protected $casts = ['price' => 'decimal:2', 'requires_prescription' => 'boolean', 'is_active' => 'boolean'];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}

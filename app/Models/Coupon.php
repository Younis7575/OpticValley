<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order', 'max_discount', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active'];

    protected $casts = [
        'value' => 'decimal:2', 'min_order' => 'decimal:2', 'max_discount' => 'decimal:2',
        'starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean',
    ];

    public function setCodeAttribute($v): void
    {
        $this->attributes['code'] = strtoupper(trim($v));
    }

    /** Returns null when valid for this subtotal, otherwise a customer-facing reason. */
    public function invalidReason(float $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'This coupon is not active.';
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'This coupon is not active yet.';
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'This coupon has expired.';
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'This coupon has reached its usage limit.';
        }
        if ($subtotal < (float) $this->min_order) {
            return 'Minimum order of '.money($this->min_order).' required for this coupon.';
        }

        return null;
    }

    public function discountFor(float $subtotal): float
    {
        $d = $this->type === 'percent' ? $subtotal * ((float) $this->value / 100) : (float) $this->value;
        if ($this->max_discount !== null) {
            $d = min($d, (float) $this->max_discount);
        }

        return round(min($d, $subtotal), 2);
    }
}

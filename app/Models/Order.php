<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'shipping' => 'decimal:2', 'tax' => 'decimal:2',
        'total' => 'decimal:2', 'refunded_amount' => 'decimal:2',
        'shipped_at' => 'datetime', 'delivered_at' => 'datetime', 'cancelled_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped',
        'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded',
    ];

    public const PAYMENT_STATUSES = ['unpaid' => 'Unpaid', 'paid' => 'Paid', 'partially_refunded' => 'Partially refunded', 'refunded' => 'Refunded'];

    public const PAYMENT_METHODS = ['cod' => 'Cash on Delivery', 'bank_transfer' => 'Bank Transfer', 'online' => 'Online Payment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }

    public static function generateNumber(): string
    {
        do {
            $n = 'OV'.now()->format('ymd').strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('order_number', $n)->exists());

        return $n;
    }
}

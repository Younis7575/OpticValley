<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\LensOption;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * Session-backed cart. Only IDs and customer choices are stored; prices and
 * stock are always re-read from the database so totals can never be tampered with.
 */
class Cart
{
    private const KEY = 'cart.items';

    private const COUPON = 'cart.coupon';

    private ?Collection $lines = null;

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Product $product, int $qty = 1, ?int $variantId = null, ?int $lensId = null, ?array $prescription = null, ?string $prescriptionFile = null): string
    {
        $key = md5(json_encode([$product->id, $variantId, $lensId, $prescription, $prescriptionFile]));
        $items = $this->raw();
        $current = $items[$key]['qty'] ?? 0;
        $items[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'lens_id' => $lensId,
            'prescription' => $prescription,
            'prescription_file' => $prescriptionFile,
            'qty' => min($current + $qty, 20),
        ];
        session([self::KEY => $items]);
        $this->lines = null;

        return $key;
    }

    public function update(string $key, int $qty): void
    {
        $items = $this->raw();
        if (! isset($items[$key])) {
            return;
        }
        if ($qty < 1) {
            unset($items[$key]);
        } else {
            $items[$key]['qty'] = min($qty, 20);
        }
        session([self::KEY => $items]);
        $this->lines = null;
    }

    public function remove(string $key): void
    {
        $this->update($key, 0);
    }

    public function clear(): void
    {
        session()->forget([self::KEY, self::COUPON]);
        $this->lines = null;
    }

    public function count(): int
    {
        return (int) collect($this->raw())->sum('qty');
    }

    /** Hydrated lines: product, variant, lens, unit price, line total, stock check. */
    public function lines(): Collection
    {
        if ($this->lines !== null) {
            return $this->lines;
        }
        $raw = collect($this->raw());
        $products = Product::with('primaryImage')->active()->whereIn('id', $raw->pluck('product_id'))->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $raw->pluck('variant_id')->filter())->get()->keyBy('id');
        $lenses = LensOption::whereIn('id', $raw->pluck('lens_id')->filter())->get()->keyBy('id');

        return $this->lines = $raw->map(function ($item, $key) use ($products, $variants, $lenses) {
            $product = $products[$item['product_id']] ?? null;
            if (! $product) {
                return null;
            }
            $variant = $item['variant_id'] ? ($variants[$item['variant_id']] ?? null) : null;
            $lens = $item['lens_id'] ? ($lenses[$item['lens_id']] ?? null) : null;
            $unit = $product->final_price + (float) ($variant?->price_adjustment ?? 0) + (float) ($lens?->price ?? 0);
            $available = $variant ? $variant->stock : $product->stock;

            return (object) [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'lens' => $lens,
                'prescription' => $item['prescription'],
                'prescription_file' => $item['prescription_file'],
                'qty' => $item['qty'],
                'unit_price' => round($unit, 2),
                'line_total' => round($unit * $item['qty'], 2),
                'available' => $available,
                'stock_ok' => $available >= $item['qty'],
            ];
        })->filter()->values();
    }

    public function subtotal(): float
    {
        return round($this->lines()->sum('line_total'), 2);
    }

    public function applyCoupon(string $code): ?string
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();
        if (! $coupon) {
            return 'Invalid coupon code.';
        }
        if ($reason = $coupon->invalidReason($this->subtotal())) {
            return $reason;
        }
        session([self::COUPON => $coupon->code]);

        return null;
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON);
    }

    public function coupon(): ?Coupon
    {
        $code = session(self::COUPON);
        if (! $code) {
            return null;
        }
        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon || $coupon->invalidReason($this->subtotal())) {
            return null;
        }

        return $coupon;
    }

    public function discount(): float
    {
        return $this->coupon()?->discountFor($this->subtotal()) ?? 0.0;
    }

    public function shipping(): float
    {
        $subtotal = $this->subtotal();
        if ($subtotal <= 0) {
            return 0;
        }
        $threshold = (float) Setting::get('free_shipping_threshold', 0);
        if ($threshold > 0 && ($subtotal - $this->discount()) >= $threshold) {
            return 0;
        }

        return (float) Setting::get('shipping_flat_rate', 250);
    }

    public function tax(): float
    {
        $rate = (float) Setting::get('tax_rate', 0);

        return round(max($this->subtotal() - $this->discount(), 0) * $rate / 100, 2);
    }

    public function total(): float
    {
        return round(max($this->subtotal() - $this->discount(), 0) + $this->shipping() + $this->tax(), 2);
    }

    public function summary(): array
    {
        return [
            'subtotal' => $this->subtotal(),
            'discount' => $this->discount(),
            'shipping' => $this->shipping(),
            'tax' => $this->tax(),
            'total' => $this->total(),
            'coupon' => $this->coupon()?->code,
        ];
    }
}

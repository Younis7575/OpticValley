<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private Cart $cart) {}

    /** Creates the order atomically: re-checks stock under row locks, decrements stock, consumes coupon. */
    public function place(array $data, ?User $user): Order
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        return DB::transaction(function () use ($data, $user, $lines) {
            foreach ($lines as $line) {
                if ($line->variant) {
                    $locked = ProductVariant::whereKey($line->variant->id)->lockForUpdate()->first();
                } else {
                    $locked = Product::whereKey($line->product->id)->lockForUpdate()->first();
                }
                if (! $locked || $locked->stock < $line->qty) {
                    throw ValidationException::withMessages([
                        'cart' => "Sorry, only {$locked?->stock} left of \"{$line->product->name}\". Please update your cart.",
                    ]);
                }
            }

            $summary = $this->cart->summary();
            $coupon = $summary['coupon'] ? Coupon::where('code', $summary['coupon'])->lockForUpdate()->first() : null;

            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'user_id' => $user?->id,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'subtotal' => $summary['subtotal'],
                'discount' => $summary['discount'],
                'shipping' => $summary['shipping'],
                'tax' => $summary['tax'],
                'total' => $summary['total'],
                'coupon_code' => $coupon?->code,
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'shipping_line1' => $data['line1'],
                'shipping_line2' => $data['line2'] ?? null,
                'shipping_city' => $data['city'],
                'shipping_province' => $data['province'] ?? null,
                'shipping_postal_code' => $data['postal_code'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_variant_id' => $line->variant?->id,
                    'name' => $line->product->name,
                    'sku' => $line->variant?->sku ?: $line->product->sku,
                    'variant_name' => $line->variant?->name,
                    'lens_name' => $line->lens?->name,
                    'lens_price' => $line->lens?->price ?? 0,
                    'prescription' => $line->prescription,
                    'prescription_file' => $line->prescription_file,
                    'unit_price' => $line->unit_price,
                    'quantity' => $line->qty,
                    'line_total' => $line->line_total,
                ]);
                if ($line->variant) {
                    ProductVariant::whereKey($line->variant->id)->decrement('stock', $line->qty);
                }
                Product::whereKey($line->product->id)->update([
                    'stock' => DB::raw('CASE WHEN stock >= '.(int) $line->qty.' THEN stock - '.(int) $line->qty.' ELSE 0 END'),
                    'sold_count' => DB::raw('sold_count + '.(int) $line->qty),
                ]);
            }

            $coupon?->increment('used_count');
            $order->histories()->create(['status' => 'pending', 'note' => 'Order placed']);

            return $order;
        });
    }

    public function changeStatus(Order $order, string $status, ?string $note = null): void
    {
        DB::transaction(function () use ($order, $status, $note) {
            $wasCancelled = in_array($order->status, ['cancelled', 'refunded'], true);
            $nowCancelled = in_array($status, ['cancelled', 'refunded'], true);

            if (! $wasCancelled && $nowCancelled) {
                $this->restock($order);
            }

            $order->status = $status;
            match ($status) {
                'shipped' => $order->shipped_at ??= now(),
                'delivered' => $order->delivered_at ??= now(),
                'cancelled' => $order->cancelled_at ??= now(),
                default => null,
            };
            if ($status === 'delivered' && $order->payment_method === 'cod') {
                $order->payment_status = 'paid';
            }
            $order->save();
            $order->histories()->create(['status' => $status, 'note' => $note, 'user_id' => auth()->id()]);
            ActivityLog::record('order.status', $order, "Order {$order->order_number} → {$status}");
        });
    }

    public function refund(Order $order, float $amount, ?string $note = null): void
    {
        DB::transaction(function () use ($order, $amount, $note) {
            $max = (float) $order->total - (float) $order->refunded_amount;
            $amount = round(min($amount, $max), 2);
            $order->refunded_amount = (float) $order->refunded_amount + $amount;
            $full = (float) $order->refunded_amount >= (float) $order->total;
            $order->payment_status = $full ? 'refunded' : 'partially_refunded';
            $order->save();
            $order->histories()->create(['status' => $order->status, 'note' => 'Refunded '.money($amount).($note ? " — $note" : ''), 'user_id' => auth()->id()]);
            if ($full && $order->status !== 'refunded') {
                $this->changeStatus($order, 'refunded', 'Full refund issued');
            }
            ActivityLog::record('order.refund', $order, "Refund {$amount} on {$order->order_number}");
        });
    }

    private function restock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                ProductVariant::whereKey($item->product_variant_id)->increment('stock', $item->quantity);
            }
            if ($item->product_id) {
                Product::whereKey($item->product_id)->update([
                    'stock' => DB::raw('stock + '.(int) $item->quantity),
                    'sold_count' => DB::raw('CASE WHEN sold_count >= '.(int) $item->quantity.' THEN sold_count - '.(int) $item->quantity.' ELSE 0 END'),
                ]);
            }
        }
    }
}

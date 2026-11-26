@php $s = $cart->summary(); @endphp
<dl class="space-y-3 text-sm">
    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-semibold">{{ money($s['subtotal']) }}</dd></div>
    @if ($s['discount'] > 0)
        <div class="flex justify-between text-brand"><dt>Discount ({{ $s['coupon'] }})</dt><dd class="font-semibold">− {{ money($s['discount']) }}</dd></div>
    @endif
    <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd class="font-semibold">{{ $s['shipping'] > 0 ? money($s['shipping']) : 'Free' }}</dd></div>
    @if ($s['tax'] > 0)
        <div class="flex justify-between"><dt class="text-muted">Tax</dt><dd class="font-semibold">{{ money($s['tax']) }}</dd></div>
    @endif
    <div class="flex items-baseline justify-between border-t border-line pt-4"><dt class="font-bold">Total</dt><dd class="font-display text-2xl">{{ money($s['total']) }}</dd></div>
</dl>
@php $threshold = (float) setting('free_shipping_threshold', 0); $left = $threshold - ($s['subtotal'] - $s['discount']); @endphp
@if ($threshold > 0 && $left > 0)
    <div class="mt-5 rounded-2xl bg-brand-50 p-4 text-xs text-brand">
        <p>Add <strong>{{ money($left) }}</strong> more for <strong>free delivery</strong>.</p>
        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white"><div class="h-full rounded-full bg-brand" style="width: {{ min(100, (($s['subtotal'] - $s['discount']) / $threshold) * 100) }}%"></div></div>
    </div>
@endif

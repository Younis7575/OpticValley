@extends('admin.layout')
@section('title', 'Order '.$order->order_number)
@section('actions')
    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="a-btn-outline">Invoice</a>
    @php $wa = preg_replace('/\D/', '', $order->customer_phone); $wa = str_starts_with($wa, '0') ? '92'.substr($wa, 1) : $wa; @endphp
    <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Assalam o Alaikum '.$order->customer_name.', this is '.setting('store_name').' regarding your order '.$order->order_number.'.') }}" target="_blank" rel="noopener" class="a-btn bg-[#25D366] text-white hover:opacity-90">WhatsApp</a>
@endsection
@section('content')
<div class="grid gap-6 xl:grid-cols-[1fr_380px]">
    <div class="space-y-6">
        <div class="a-card flex flex-wrap items-center gap-4 p-5 text-sm">
            <x-admin.status :status="$order->status"/><x-admin.status :status="$order->payment_status"/>
            <span class="text-muted">Placed {{ $order->created_at->format('d M Y, g:i a') }}</span>
            <span class="text-muted">· {{ $order->payment_method_label }}</span>
            @if ($order->coupon_code)<span class="a-badge bg-brand-50 text-brand">Coupon {{ $order->coupon_code }}</span>@endif
        </div>

        <div class="a-card overflow-hidden">
            <div class="border-b border-line px-5 py-4 font-bold">Items</div>
            @foreach ($order->items as $item)
                <div class="flex gap-4 border-b border-line/70 px-5 py-4 last:border-0">
                    <img src="{{ $item->product?->image_url ?? asset('images/placeholder.svg') }}" alt="" class="size-16 rounded-xl bg-paper object-cover">
                    <div class="flex-1 text-sm">
                        <p class="font-semibold">@if ($item->product)<a href="{{ route('admin.products.edit', $item->product) }}" class="hover:underline">{{ $item->name }}</a>@else{{ $item->name }}@endif</p>
                        <p class="text-xs text-muted">SKU {{ $item->sku }} @if ($item->variant_name)· {{ $item->variant_name }}@endif</p>
                        @if ($item->lens_name)<p class="mt-1 text-xs"><strong>Lens:</strong> {{ $item->lens_name }} (+{{ money($item->lens_price) }})</p>@endif
                        @if ($item->prescription)
                            <div class="mt-2 overflow-x-auto rounded-lg bg-paper p-3 text-xs">
                                <table class="w-full text-center"><thead><tr class="text-muted"><th></th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th></tr></thead><tbody>
                                    @foreach (['od' => 'OD (R)', 'os' => 'OS (L)'] as $eye => $lbl)<tr><th class="text-left">{{ $lbl }}</th>@foreach (['sph', 'cyl', 'axis', 'add'] as $f)<td>{{ $item->prescription[$eye.'_'.$f] ?? '—' }}</td>@endforeach</tr>@endforeach
                                </tbody></table>
                                @if (! empty($item->prescription['pd']))<p class="mt-1">PD: {{ $item->prescription['pd'] }}</p>@endif
                                @if (! empty($item->prescription['note']))<p class="mt-1">Note: {{ $item->prescription['note'] }}</p>@endif
                            </div>
                        @endif
                        @if ($item->prescription_file)<a href="{{ route('admin.orders.prescription', [$order, $item]) }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-brand underline">View uploaded prescription</a>@endif
                    </div>
                    <div class="text-right text-sm"><p>{{ money($item->unit_price) }} × {{ $item->quantity }}</p><p class="font-bold">{{ money($item->line_total) }}</p></div>
                </div>
            @endforeach
            <dl class="space-y-1.5 border-t border-line bg-paper/50 px-5 py-4 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                @if ($order->discount > 0)<div class="flex justify-between text-brand"><dt>Discount</dt><dd>− {{ money($order->discount) }}</dd></div>@endif
                <div class="flex justify-between"><dt class="text-muted">Shipping</dt><dd>{{ money($order->shipping) }}</dd></div>
                @if ($order->tax > 0)<div class="flex justify-between"><dt class="text-muted">Tax</dt><dd>{{ money($order->tax) }}</dd></div>@endif
                <div class="flex justify-between text-base font-bold"><dt>Total</dt><dd>{{ money($order->total) }}</dd></div>
                @if ($order->refunded_amount > 0)<div class="flex justify-between text-sale"><dt>Refunded</dt><dd>− {{ money($order->refunded_amount) }}</dd></div>@endif
            </dl>
        </div>

        <div class="a-card">
            <div class="border-b border-line px-5 py-4 font-bold">Timeline</div>
            <ul class="space-y-4 p-5 text-sm">
                @foreach ($order->histories as $h)
                    <li class="flex gap-3"><span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand"></span><div><p><x-admin.status :status="$h->status"/> @if ($h->note)<span class="ml-1">{{ $h->note }}</span>@endif</p><p class="mt-0.5 text-xs text-muted">{{ $h->created_at->format('d M Y, g:i a') }} · {{ $h->user?->name ?? 'Customer / system' }}</p></div></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="space-y-6">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="a-card space-y-4 p-5">
            @csrf @method('PUT')
            <h2 class="font-bold">Process order</h2>
            <x-admin.field name="status" type="select" label="Order status" :value="$order->status" :options="\App\Models\Order::STATUSES" help="Cancelling or refunding returns items to stock automatically."/>
            <x-admin.field name="payment_status" type="select" label="Payment status" :value="$order->payment_status" :options="\App\Models\Order::PAYMENT_STATUSES"/>
            <div class="grid grid-cols-2 gap-3"><x-admin.field name="courier" label="Courier" :value="$order->courier" placeholder="TCS, Leopards…"/><x-admin.field name="tracking_number" label="Tracking #" :value="$order->tracking_number"/></div>
            <x-admin.field name="note" label="Timeline note (visible to customer)" placeholder="e.g. Lenses fitted, dispatching today"/>
            <x-admin.field name="admin_notes" type="textarea" rows="3" label="Internal notes" :value="$order->admin_notes"/>
            <button class="a-btn-primary w-full">Update order</button>
        </form>

        <div class="a-card space-y-1 p-5 text-sm">
            <h2 class="mb-2 font-bold">Customer</h2>
            <p class="font-semibold">{{ $order->customer_name }}</p>
            <p><a href="tel:{{ $order->customer_phone }}" class="hover:underline">{{ $order->customer_phone }}</a></p>
            <p><a href="mailto:{{ $order->customer_email }}" class="hover:underline">{{ $order->customer_email }}</a></p>
            @if ($order->user)<a href="{{ route('admin.customers.show', $order->user) }}" class="text-xs font-semibold text-brand">View customer profile →</a>@else<p class="text-xs text-muted">Guest checkout</p>@endif
            <h3 class="pt-3 font-bold">Shipping address</h3>
            <p class="text-ink-2">{{ $order->shipping_line1 }}@if ($order->shipping_line2)<br>{{ $order->shipping_line2 }}@endif<br>{{ $order->shipping_city }}{{ $order->shipping_province ? ', '.$order->shipping_province : '' }} {{ $order->shipping_postal_code }}</p>
            @if ($order->notes)<h3 class="pt-3 font-bold">Customer note</h3><p class="rounded-lg bg-gold/10 p-3 text-ink-2">{{ $order->notes }}</p>@endif
        </div>

        @if ((float) $order->refunded_amount < (float) $order->total)
            <form method="POST" action="{{ route('admin.orders.refund', $order) }}" class="a-card space-y-3 p-5" onsubmit="return confirm('Record this refund?')">
                @csrf
                <h2 class="font-bold">Refund</h2>
                <x-admin.field name="amount" type="number" step="0.01" min="1" :max="(float) $order->total - (float) $order->refunded_amount" label="Amount" :value="(float) $order->total - (float) $order->refunded_amount"/>
                <x-admin.field name="reason" label="Reason"/>
                <p class="a-help">Records the refund on the order. Send the money to the customer through your bank / wallet.</p>
                <button class="a-btn-danger w-full">Record refund</button>
            </form>
        @endif
    </div>
</div>
@endsection

@extends('store.account._layout')
@section('title', 'Order '.$order->order_number)
@section('heading', 'Order '.$order->order_number)
@section('account')
    <div class="card p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-muted">Placed {{ $order->created_at->format('d M Y, g:i a') }}</p>
            @include('store.account._status', ['status' => $order->status])
        </div>
        @include('store.account._timeline')
    </div>
    <div class="mt-6 grid gap-6 md:grid-cols-[1fr_300px]">
        <div class="card divide-y divide-line">
            @foreach ($order->items as $item)
                <div class="flex gap-4 p-5">
                    <div class="size-20 shrink-0 overflow-hidden rounded-2xl bg-white ring-1 ring-line">@if ($item->product)<img src="{{ $item->product->image_url }}" alt="" class="size-full object-contain p-1">@endif</div>
                    <div class="flex-1 text-sm">
                        <p class="font-semibold">{{ $item->name }}</p>
                        <p class="text-xs text-muted">{{ collect([$item->variant_name, $item->lens_name])->filter()->join(' · ') }} · Qty {{ $item->quantity }}</p>
                        @if ($item->product)<a href="{{ route('product.show', $item->product) }}" class="mt-2 inline-block text-xs font-semibold underline underline-offset-4">Buy again</a>@endif
                    </div>
                    <p class="text-sm font-bold">{{ money($item->line_total) }}</p>
                </div>
            @endforeach
        </div>
        <div class="space-y-6">
            <div class="card p-5 text-sm">
                <p class="label">Delivery address</p>
                <p class="font-semibold">{{ $order->customer_name }}</p>
                <p class="text-ink-2">{{ $order->shipping_line1 }}@if ($order->shipping_line2), {{ $order->shipping_line2 }}@endif<br>{{ $order->shipping_city }}{{ $order->shipping_province ? ', '.$order->shipping_province : '' }}<br>{{ $order->customer_phone }}</p>
            </div>
            <div class="card space-y-2 p-5 text-sm">
                <div class="flex justify-between"><span class="text-muted">Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
                @if ($order->discount > 0)<div class="flex justify-between text-brand"><span>Discount</span><span>− {{ money($order->discount) }}</span></div>@endif
                <div class="flex justify-between"><span class="text-muted">Delivery</span><span>{{ $order->shipping > 0 ? money($order->shipping) : 'Free' }}</span></div>
                <div class="flex justify-between border-t border-line pt-2 font-bold"><span>Total</span><span>{{ money($order->total) }}</span></div>
                <p class="pt-1 text-xs text-muted">{{ $order->payment_method_label }} · {{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status }}</p>
            </div>
            @if ($order->canBeCancelled())
                <form method="POST" action="{{ route('account.order.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">@csrf<button class="btn-outline w-full !border-sale/30 !text-sale">Cancel order</button></form>
            @endif
        </div>
    </div>
@endsection

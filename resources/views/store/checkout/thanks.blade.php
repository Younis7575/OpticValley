@extends('layouts.store')
@section('title', 'Thank you for your order')
@section('noindex', true)

@section('content')
<section class="container-x max-w-3xl py-16 md:py-24">
    <div class="text-center">
        <span class="mx-auto grid size-20 place-items-center rounded-full bg-brand text-white shadow-xl shadow-brand/30 animate-rise"><x-icon name="check" class="size-9"/></span>
        <p class="eyebrow mt-8">Order confirmed</p>
        <h1 class="mt-2 text-4xl md:text-5xl">Thank you, {{ \Illuminate\Support\Str::before($order->customer_name, ' ') }}!</h1>
        <p class="mx-auto mt-4 max-w-md text-muted">Your order <strong class="text-ink">{{ $order->order_number }}</strong> has been placed. We'll confirm it by phone or WhatsApp shortly{{ $order->customer_email ? ', and a summary is on its way to '.$order->customer_email : '' }}.</p>
    </div>

    <div class="card mt-12 p-6 md:p-8">
        <div class="grid gap-6 text-sm sm:grid-cols-3">
            <div><p class="label">Order number</p><p class="font-semibold">{{ $order->order_number }}</p></div>
            <div><p class="label">Payment</p><p class="font-semibold">{{ $order->payment_method_label }}</p></div>
            <div><p class="label">Deliver to</p><p class="font-semibold">{{ $order->shipping_city }}</p></div>
        </div>
        @if ($order->payment_method === 'bank_transfer' && setting('payment_bank_details'))
            <div class="mt-6 whitespace-pre-line rounded-2xl bg-sand p-5 text-sm"><strong>Bank details</strong>
{{ setting('payment_bank_details') }}
Please use <strong>{{ $order->order_number }}</strong> as the payment reference and share the receipt on WhatsApp.</div>
        @endif
        <div class="mt-8 divide-y divide-line border-y border-line">
            @foreach ($order->items as $item)
                <div class="flex justify-between gap-4 py-4 text-sm">
                    <div><p class="font-semibold">{{ $item->name }} <span class="text-muted">× {{ $item->quantity }}</span></p>
                        <p class="text-xs text-muted">{{ collect([$item->variant_name, $item->lens_name])->filter()->join(' · ') }}</p></div>
                    <p class="font-semibold">{{ money($item->line_total) }}</p>
                </div>
            @endforeach
        </div>
        <dl class="mt-5 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
            @if ($order->discount > 0)<div class="flex justify-between text-brand"><dt>Discount</dt><dd>− {{ money($order->discount) }}</dd></div>@endif
            <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd>{{ $order->shipping > 0 ? money($order->shipping) : 'Free' }}</dd></div>
            @if ($order->tax > 0)<div class="flex justify-between"><dt class="text-muted">Tax</dt><dd>{{ money($order->tax) }}</dd></div>@endif
            <div class="flex justify-between border-t border-line pt-3 text-base font-bold"><dt>Total</dt><dd>{{ money($order->total) }}</dd></div>
        </dl>
    </div>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ route('track') }}" class="btn-outline">Track order</a>
        <a href="{{ route('shop') }}" class="btn-primary">Continue shopping</a>
    </div>
</section>
@endsection

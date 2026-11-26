@extends('layouts.store')
@section('title', 'Track your order')
@section('noindex', true)
@section('content')
<section class="container-x max-w-3xl py-14 md:py-20">
    <div class="text-center"><p class="eyebrow">Order status</p><h1 class="mt-2 text-4xl md:text-5xl">Track your order</h1><p class="mt-3 text-sm text-muted">Enter your order number and the phone number used at checkout.</p></div>
    <form method="POST" action="{{ route('track.lookup') }}" class="card mt-10 grid gap-4 p-6 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
        @csrf
        <div><label class="label" for="t-on">Order number</label><input id="t-on" name="order_number" value="{{ old('order_number', $order->order_number ?? '') }}" required placeholder="OV…" class="field uppercase"></div>
        <div><label class="label" for="t-ph">Phone</label><input id="t-ph" name="phone" value="{{ old('phone') }}" required placeholder="03XX XXXXXXX" class="field"></div>
        <button class="btn-primary !py-3">Track</button>
    </form>
    @isset($order)
        <div class="card mt-8 p-6 md:p-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div><p class="font-semibold">{{ $order->order_number }}</p><p class="text-xs text-muted">Placed {{ $order->created_at->format('d M Y') }} · {{ money($order->total) }}</p></div>
                @include('store.account._status', ['status' => $order->status])
            </div>
            @include('store.account._timeline')
            <ul class="mt-8 space-y-3 border-t border-line pt-6 text-sm">
                @foreach ($order->histories as $h)
                    <li class="flex gap-3"><span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand"></span><div><p class="font-semibold">{{ \App\Models\Order::STATUSES[$h->status] ?? $h->status }} @if ($h->note && ! str_starts_with($h->note, 'Refunded'))<span class="font-normal text-muted">— {{ $h->note }}</span>@endif</p><p class="text-xs text-muted">{{ $h->created_at->format('d M Y, g:i a') }}</p></div></li>
                @endforeach
            </ul>
        </div>
    @endisset
</section>
@endsection

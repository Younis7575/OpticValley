@extends('layouts.store')
@section('title', 'Your cart')
@section('noindex', true)

@section('content')
<section class="container-x py-12 md:py-16">
    <h1 class="text-4xl md:text-5xl">Your cart</h1>
    @if ($lines->isEmpty())
        <div class="card mt-10 grid place-items-center px-6 py-24 text-center">
            <span class="grid size-16 place-items-center rounded-full bg-sand"><x-icon name="bag" class="size-7"/></span>
            <h2 class="mt-5 text-2xl">Your cart is empty</h2>
            <p class="mt-2 text-sm text-muted">Discover frames you'll love wearing every day.</p>
            <a href="{{ route('shop') }}" class="btn-primary mt-7">Start shopping</a>
        </div>
    @else
        <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_400px]">
            <div class="divide-y divide-line border-y border-line">
                @foreach ($lines as $line)
                    <div class="py-6">
                        @component('store.checkout._line', ['line' => $line])
                            <div class="mt-4 flex items-center gap-4">
                                <form action="{{ route('cart.update', $line->key) }}" method="POST" class="flex items-center rounded-full border border-line bg-white">
                                    @csrf @method('PATCH')
                                    <button name="qty" value="{{ $line->qty - 1 }}" class="grid size-9 place-items-center" aria-label="Decrease"><x-icon name="minus" class="size-3.5"/></button>
                                    <span class="w-7 text-center text-sm font-semibold">{{ $line->qty }}</span>
                                    <button name="qty" value="{{ $line->qty + 1 }}" class="grid size-9 place-items-center" @disabled($line->qty >= $line->available) aria-label="Increase"><x-icon name="plus" class="size-3.5"/></button>
                                </form>
                                <form action="{{ route('cart.remove', $line->key) }}" method="POST">@csrf @method('DELETE')<button class="flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-sale"><x-icon name="trash" class="size-4"/> Remove</button></form>
                            </div>
                        @endcomponent
                    </div>
                @endforeach
            </div>
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="card p-6 md:p-7">
                    <h2 class="text-2xl">Order summary</h2>
                    <div class="mt-5">
                        @if ($cart->coupon())
                            <div class="flex items-center justify-between rounded-2xl bg-brand-50 px-4 py-3 text-sm">
                                <span class="flex items-center gap-2 font-semibold text-brand"><x-icon name="tag" class="size-4"/> {{ $cart->coupon()->code }}</span>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST">@csrf @method('DELETE')<button class="text-xs font-semibold text-muted hover:text-sale">Remove</button></form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input name="code" placeholder="Coupon code" class="field !rounded-full uppercase" required aria-label="Coupon code">
                                <button class="btn-outline shrink-0 !px-5">Apply</button>
                            </form>
                        @endif
                    </div>
                    <div class="mt-6">@include('store.checkout._summary')</div>
                    <a href="{{ route('checkout') }}" class="btn-primary mt-6 w-full !py-4 {{ $lines->every->stock_ok ? '' : 'pointer-events-none opacity-50' }}">Checkout securely <x-icon name="arrow-right" class="size-4"/></a>
                    <a href="{{ route('shop') }}" class="mt-3 block text-center text-xs font-semibold text-muted hover:text-ink">Continue shopping</a>
                </div>
            </aside>
        </div>
    @endif
</section>
@endsection

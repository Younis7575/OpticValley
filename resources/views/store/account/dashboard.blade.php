@extends('store.account._layout')
@section('title', 'My account')
@section('heading', 'Hello, '.\Illuminate\Support\Str::before(auth()->user()->name, ' '))
@section('account')
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ([['Orders', $stats['orders'], 'account.orders'], ['In progress', $stats['active'], 'account.orders'], ['Wishlist', $stats['wishlist'], 'account.wishlist'], ['Addresses', $stats['addresses'], 'account.addresses']] as [$l, $v, $r])
            <a href="{{ route($r) }}" class="card p-5 transition hover:border-ink"><p class="text-xs font-semibold uppercase tracking-widest text-muted">{{ $l }}</p><p class="mt-2 font-display text-4xl">{{ $v }}</p></a>
        @endforeach
    </div>
    <div class="card mt-8 overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-6 py-4"><h2 class="text-xl">Recent orders</h2><a href="{{ route('account.orders') }}" class="text-xs font-semibold text-muted hover:text-ink">View all</a></div>
        @forelse ($recent as $o)
            <a href="{{ route('account.order', $o) }}" class="flex flex-wrap items-center justify-between gap-3 border-b border-line/70 px-6 py-4 text-sm last:border-0 hover:bg-sand/40">
                <span class="font-semibold">{{ $o->order_number }}</span><span class="text-muted">{{ $o->created_at->format('d M Y') }}</span>
                @include('store.account._status', ['status' => $o->status])<span class="font-bold">{{ money($o->total) }}</span>
            </a>
        @empty
            <p class="px-6 py-10 text-center text-sm text-muted">No orders yet. <a href="{{ route('shop') }}" class="font-semibold text-ink underline">Start shopping</a></p>
        @endforelse
    </div>
@endsection

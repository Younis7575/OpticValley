@extends('admin.layout')
@section('title', $customer->name)
@section('content')
<div class="grid gap-6 xl:grid-cols-[340px_1fr]">
    <div class="space-y-6">
        <div class="a-card p-5 text-sm">
            <div class="flex items-center gap-3"><span class="grid size-12 place-items-center rounded-full bg-brand text-lg font-bold text-white">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span><div><p class="font-bold">{{ $customer->name }}</p><p class="text-muted">{{ $customer->email }}</p></div></div>
            <dl class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-paper p-3"><dt class="text-xs text-muted">Orders</dt><dd class="text-lg font-bold">{{ $orders->total() }}</dd></div>
                <div class="rounded-xl bg-paper p-3"><dt class="text-xs text-muted">Spent</dt><dd class="text-lg font-bold">{{ money($spent) }}</dd></div>
                <div class="rounded-xl bg-paper p-3"><dt class="text-xs text-muted">Wishlist</dt><dd class="text-lg font-bold">{{ $wishlist }}</dd></div>
                <div class="rounded-xl bg-paper p-3"><dt class="text-xs text-muted">Last login</dt><dd class="font-semibold">{{ $customer->last_login_at?->diffForHumans() ?? '—' }}</dd></div>
            </dl>
            <p class="mt-4">Phone: {{ $customer->phone ?? '—' }}</p>
            <p>Joined: {{ $customer->created_at->format('d M Y') }}</p>
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="mt-4 border-t border-line pt-4">@csrf @method('PUT')
                <x-admin.toggle name="is_active" label="Account active" :checked="$customer->is_active" help="Blocked customers cannot sign in."/>
                <button class="a-btn-outline mt-3 w-full">Save status</button>
            </form>
        </div>
        <div class="a-card p-5 text-sm"><h2 class="mb-3 font-bold">Addresses</h2>
            @forelse ($customer->addresses as $a)<div class="mb-3 rounded-xl bg-paper p-3"><p class="font-semibold">{{ $a->label }} @if ($a->is_default)<span class="a-badge bg-brand-50 text-brand">Default</span>@endif</p><p class="text-ink-2">{{ $a->line1 }}, {{ $a->city }} · {{ $a->phone }}</p></div>@empty<p class="text-muted">No saved addresses.</p>@endforelse
        </div>
    </div>
    <div class="a-card overflow-x-auto">
        <div class="border-b border-line px-5 py-4 font-bold">Order history</div>
        <table class="a-table min-w-[520px]">
            <thead><tr><th>Order</th><th>Date</th><th>Status</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @forelse ($orders as $o)<tr><td><a href="{{ route('admin.orders.show', $o) }}" class="font-semibold hover:underline">{{ $o->order_number }}</a></td><td class="text-muted">{{ $o->created_at->format('d M Y') }}</td><td><x-admin.status :status="$o->status"/></td><td class="text-right font-semibold">{{ money($o->total) }}</td></tr>@empty<x-admin.empty colspan="4" text="No orders yet."/>@endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $orders->links() }}</div>
    </div>
</div>
@endsection

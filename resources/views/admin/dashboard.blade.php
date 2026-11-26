@extends('admin.layout')
@section('title', 'Dashboard')
@section('actions')
    <form method="GET" class="hidden sm:block">
        <select name="days" onchange="this.form.submit()" class="a-input !w-auto !py-2">
            @foreach ([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last 12 months'] as $d => $l)<option value="{{ $d }}" @selected($days === $d)>{{ $l }}</option>@endforeach
        </select>
    </form>
@endsection
@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
        $cards = [
            ['Revenue', money($stats['revenue']), $stats['revenue_change'] !== null ? (($stats['revenue_change'] >= 0 ? '+' : '').$stats['revenue_change'].'% vs previous period') : 'Last '.$days.' days', $stats['revenue_change'] !== null && $stats['revenue_change'] < 0 ? 'text-sale' : 'text-brand'],
            ['Orders', number_format($stats['orders']), 'Avg. order '.money($stats['aov']), 'text-muted'],
            ['Customers', number_format($stats['customers']), '+'.$stats['new_customers'].' new in period', 'text-muted'],
            ['Products', number_format($stats['products']), $stats['active_products'].' active in store', 'text-muted'],
        ];
    @endphp
    @foreach ($cards as [$l, $v, $sub, $cls])
        <div class="a-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-muted">{{ $l }}</p><p class="mt-2 font-display text-3xl">{{ $v }}</p><p class="mt-1 text-xs {{ $cls }}">{{ $sub }}</p></div>
    @endforeach
</div>

<div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([['Pending', $stats['pending'], 'pending', 'bg-gold'], ['In progress', $stats['processing'], 'processing', 'bg-brand'], ['Delivered', $stats['completed'], 'delivered', 'bg-[#2f6b3f]'], ['Cancelled / refunded', $stats['cancelled'], 'cancelled', 'bg-sale']] as [$l, $v, $st, $dot])
        <a href="{{ route('admin.orders.index', ['status' => $st]) }}" class="a-card flex items-center gap-3 p-4 hover:border-ink"><span class="size-2.5 rounded-full {{ $dot }}"></span><span class="text-sm text-muted">{{ $l }}</span><span class="ml-auto text-lg font-bold">{{ $v }}</span></a>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1fr_360px]">
    <div class="a-card p-5">
        <div class="flex items-center justify-between"><h2 class="font-bold">Sales analytics</h2><span class="flex items-center gap-4 text-xs text-muted"><span class="flex items-center gap-1.5"><span class="h-0.5 w-4 bg-brand"></span>Revenue</span><span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-gold/40"></span>Orders</span></span></div>
        <div class="mt-4 h-72" x-data="revenueChart({{ Js::from($chart['labels']) }}, {{ Js::from($chart['rev']) }}, {{ Js::from($chart['ord']) }})"><canvas x-ref="canvas"></canvas></div>
    </div>
    <div class="a-card">
        <div class="flex items-center justify-between border-b border-line px-5 py-4"><h2 class="font-bold">Top selling</h2><span class="text-xs text-muted">{{ $days }} days</span></div>
        <ul class="divide-y divide-line/70">
            @forelse ($topProducts as $tp)
                <li class="flex items-center gap-3 px-5 py-3 text-sm"><span class="min-w-0 flex-1 truncate font-medium">{{ $tp->name }}</span><span class="text-muted">{{ $tp->qty }} sold</span><span class="w-24 text-right font-semibold">{{ money($tp->revenue) }}</span></li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-muted">No sales in this period yet.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1fr_360px]">
    <div class="a-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-5 py-4"><h2 class="font-bold">Recent orders</h2><a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-muted hover:text-ink">View all →</a></div>
        <div class="overflow-x-auto">
            <table class="a-table min-w-[560px]">
                <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Date</th><th class="text-right">Total</th></tr></thead>
                <tbody>
                    @forelse ($recentOrders as $o)
                        <tr><td><a href="{{ route('admin.orders.show', $o) }}" class="font-semibold hover:underline">{{ $o->order_number }}</a></td><td>{{ $o->customer_name }}<div class="text-xs text-muted">{{ $o->shipping_city }}</div></td><td><x-admin.status :status="$o->status"/></td><td class="text-muted">{{ $o->created_at->diffForHumans() }}</td><td class="text-right font-semibold">{{ money($o->total) }}</td></tr>
                    @empty
                        <x-admin.empty colspan="5" text="No orders yet. They will appear here as soon as customers check out."/>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="a-card">
        <div class="flex items-center justify-between border-b border-line px-5 py-4"><h2 class="font-bold">Low stock</h2><a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="text-xs font-semibold text-muted hover:text-ink">View →</a></div>
        <ul class="divide-y divide-line/70">
            @forelse ($lowStock as $p)
                <li class="flex items-center gap-3 px-5 py-3 text-sm"><a href="{{ route('admin.products.edit', $p) }}" class="min-w-0 flex-1 truncate font-medium hover:underline">{{ $p->name }}</a><span class="a-badge {{ $p->stock === 0 ? 'bg-sale/10 text-sale' : 'bg-gold/15 text-[#8a6a35]' }}">{{ $p->stock === 0 ? 'Out of stock' : $p->stock.' left' }}</span></li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-muted">All products are well stocked.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

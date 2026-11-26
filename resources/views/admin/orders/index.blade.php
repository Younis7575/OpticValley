@extends('admin.layout')
@section('title', 'Orders')
@section('actions')<a href="{{ route('admin.orders.export', request()->query()) }}" class="a-btn-outline">Export CSV</a>@endsection
@section('content')
<div class="no-scrollbar mb-4 flex gap-1 overflow-x-auto text-sm">
    <a href="{{ route('admin.orders.index') }}" class="shrink-0 rounded-lg px-3 py-1.5 font-semibold {{ request('status') ? 'text-muted hover:bg-white' : 'bg-ink text-white' }}">All <span class="opacity-60">{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\Order::STATUSES as $k => $l)
        <a href="{{ route('admin.orders.index', ['status' => $k]) }}" class="shrink-0 rounded-lg px-3 py-1.5 font-semibold {{ request('status') === $k ? 'bg-ink text-white' : 'text-muted hover:bg-white' }}">{{ $l }} <span class="opacity-60">{{ $counts[$k] ?? 0 }}</span></a>
    @endforeach
</div>
<div class="mb-4"><x-admin.search placeholder="Order #, name, phone, email" :filters="['payment' => ['Any payment', \App\Models\Order::PAYMENT_STATUSES]]">
    @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="date" name="from" value="{{ request('from') }}" class="a-input !w-auto" title="From"><input type="date" name="to" value="{{ request('to') }}" class="a-input !w-auto" title="To"><button class="a-btn-outline !py-2">Filter</button>
</x-admin.search></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[860px]">
        <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Items</th><th>Payment</th><th>Status</th><th class="text-right">Total</th></tr></thead>
        <tbody>
            @forelse ($orders as $o)
                <tr class="cursor-pointer" onclick="location='{{ route('admin.orders.show', $o) }}'">
                    <td class="font-semibold">{{ $o->order_number }}</td>
                    <td class="text-muted">{{ $o->created_at->format('d M, g:i a') }}</td>
                    <td>{{ $o->customer_name }}<div class="text-xs text-muted">{{ $o->customer_phone }} · {{ $o->shipping_city }}</div></td>
                    <td>{{ $o->items_count }}</td>
                    <td><div class="text-xs">{{ $o->payment_method_label }}</div><x-admin.status :status="$o->payment_status"/></td>
                    <td><x-admin.status :status="$o->status"/></td>
                    <td class="text-right font-semibold">{{ money($o->total) }}</td>
                </tr>
            @empty
                <x-admin.empty colspan="7" text="No orders match your filters."/>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

@extends('store.account._layout')
@section('title', 'My orders')
@section('heading', 'Orders')
@section('account')
    <div class="card overflow-x-auto">
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead class="border-b border-line text-xs uppercase tracking-widest text-muted"><tr><th class="px-6 py-4 font-semibold">Order</th><th class="font-semibold">Date</th><th class="font-semibold">Items</th><th class="font-semibold">Status</th><th class="px-6 text-right font-semibold">Total</th></tr></thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr class="border-b border-line/70 last:border-0 hover:bg-sand/40">
                        <td class="px-6 py-4"><a href="{{ route('account.order', $o) }}" class="font-semibold underline-offset-4 hover:underline">{{ $o->order_number }}</a></td>
                        <td class="text-muted">{{ $o->created_at->format('d M Y') }}</td><td>{{ $o->items_count }}</td>
                        <td>@include('store.account._status', ['status' => $o->status])</td>
                        <td class="px-6 text-right font-bold">{{ money($o->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-14 text-center text-muted">You haven't placed any orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
@endsection

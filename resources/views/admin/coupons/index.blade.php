@extends('admin.layout')
@section('title', 'Coupons')
@section('actions')<a href="{{ route('admin.coupons.create') }}" class="a-btn-primary">+ Add coupon</a>@endsection
@section('content')
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[760px]">
        <thead><tr><th>Code</th><th>Discount</th><th>Min. order</th><th>Usage</th><th>Valid</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($coupons as $c)
                @php $reason = $c->invalidReason(PHP_FLOAT_MAX); @endphp
                <tr><td class="font-mono font-bold">{{ $c->code }}</td><td>{{ $c->type === 'percent' ? rtrim(rtrim(number_format($c->value, 2), '0'), '.').'%' : money($c->value) }}@if ($c->max_discount)<div class="text-xs text-muted">max {{ money($c->max_discount) }}</div>@endif</td><td>{{ $c->min_order > 0 ? money($c->min_order) : '—' }}</td><td>{{ $c->used_count }}{{ $c->max_uses ? ' / '.$c->max_uses : '' }}</td><td class="text-xs text-muted">{{ $c->starts_at?->format('d M Y') ?? 'Now' }} → {{ $c->expires_at?->format('d M Y') ?? 'No expiry' }}</td><td>@if ($reason)<span class="a-badge bg-paper text-muted border border-line" title="{{ $reason }}">Inactive</span>@else<x-admin.status status="active"/>@endif</td><td class="space-x-3 text-right"><a href="{{ route('admin.coupons.edit', $c) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.coupons.destroy', $c)"/></td></tr>
            @empty
                <x-admin.empty colspan="7"/>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection

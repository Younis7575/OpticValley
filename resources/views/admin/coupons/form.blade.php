@extends('admin.layout')
@section('title', $coupon->exists ? 'Edit coupon' : 'Add coupon')
@section('content')
<form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="a-card max-w-2xl space-y-4 p-6">
    @csrf @if ($coupon->exists) @method('PUT') @endif
    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.field name="code" label="Code" :value="$coupon->code" required class="sm:col-span-1" style="text-transform:uppercase"/>
        <x-admin.field name="type" type="select" label="Type" :value="$coupon->type" :options="['percent' => 'Percentage', 'fixed' => 'Fixed amount']"/>
        <x-admin.field name="value" type="number" step="0.01" min="0" label="Value" :value="$coupon->value" required/>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.field name="min_order" type="number" step="0.01" min="0" label="Minimum order" :value="$coupon->min_order"/>
        <x-admin.field name="max_discount" type="number" step="0.01" min="0" label="Max discount" :value="$coupon->max_discount" help="For % coupons."/>
        <x-admin.field name="max_uses" type="number" min="1" label="Usage limit" :value="$coupon->max_uses" help="Empty = unlimited."/>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-admin.field name="starts_at" type="datetime-local" label="Starts" :value="$coupon->starts_at?->format('Y-m-d\TH:i')"/>
        <x-admin.field name="expires_at" type="datetime-local" label="Expires" :value="$coupon->expires_at?->format('Y-m-d\TH:i')"/>
    </div>
    <x-admin.toggle name="is_active" label="Active" :checked="$coupon->is_active"/>
    @if ($coupon->exists)<p class="text-xs text-muted">Used {{ $coupon->used_count }} times.</p>@endif
    <button class="a-btn-primary">Save coupon</button>
</form>
@endsection

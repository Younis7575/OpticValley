@extends('admin.layout')
@section('title', 'Products')
@section('actions')<a href="{{ route('admin.products.create') }}" class="a-btn-primary">+ Add product</a>@endsection
@section('content')
<div class="mb-4"><x-admin.search placeholder="Search name or SKU" :filters="['category' => ['All categories', $categories], 'brand' => ['All brands', $brands], 'stock' => ['Any stock', ['low' => 'Low stock', 'out' => 'Out of stock']], 'status' => ['Any status', ['active' => 'Active', 'inactive' => 'Hidden']]]"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[820px]">
        <thead><tr><th class="w-16"></th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td><img src="{{ $p->image_url ?? asset('images/placeholder.svg') }}" alt="" class="size-12 rounded-lg bg-paper object-cover"></td>
                    <td><a href="{{ route('admin.products.edit', $p) }}" class="font-semibold hover:underline">{{ $p->name }}</a><div class="text-xs text-muted">{{ $p->sku }} @if ($p->brand)· {{ $p->brand->name }}@endif</div></td>
                    <td class="text-muted">{{ $p->category?->name ?? '—' }}</td>
                    <td>@if ($p->hasDiscount())<span class="font-semibold text-sale">{{ money($p->sale_price) }}</span> <span class="text-xs text-muted line-through">{{ money($p->price) }}</span>@else<span class="font-semibold">{{ money($p->price) }}</span>@endif</td>
                    <td><span class="a-badge {{ $p->stock === 0 ? 'bg-sale/10 text-sale' : ($p->is_low_stock ? 'bg-gold/15 text-[#8a6a35]' : 'bg-paper text-ink border border-line') }}">{{ $p->stock }}</span></td>
                    <td><x-admin.status :status="$p->is_active ? 'active' : 'inactive'"/> @if ($p->is_featured)<span class="a-badge bg-brand-50 text-brand">Featured</span>@endif</td>
                    <td class="space-x-3 whitespace-nowrap text-right"><a href="{{ route('product.show', $p) }}" target="_blank" class="text-xs font-semibold text-muted hover:text-ink">View</a><a href="{{ route('admin.products.edit', $p) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.products.destroy', $p)"/></td>
                </tr>
            @empty
                <x-admin.empty colspan="7" text="No products found."/>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection

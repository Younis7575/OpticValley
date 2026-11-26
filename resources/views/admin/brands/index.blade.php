@extends('admin.layout')
@section('title', 'Brands')
@section('actions')<a href="{{ route('admin.brands.create') }}" class="a-btn-primary">+ Add brand</a>@endsection
@section('content')
<div class="mb-4"><x-admin.search placeholder="Search brands"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[600px]">
        <thead><tr><th>Brand</th><th>Products</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($brands as $b)
                <tr>
                    <td class="flex items-center gap-3">@if ($b->logo_url)<img src="{{ $b->logo_url }}" class="h-8 w-12 object-contain" alt="">@endif<div><p class="font-semibold">{{ $b->name }}</p><p class="text-xs text-muted">{{ $b->slug }}</p></div></td>
                    <td>{{ $b->products_count }}</td>
                    <td><x-admin.status :status="$b->is_active ? 'active' : 'inactive'"/> @if ($b->is_featured)<span class="a-badge bg-brand-50 text-brand">Featured</span>@endif</td>
                    <td class="space-x-3 text-right"><a href="{{ route('admin.brands.edit', $b) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.brands.destroy', $b)" confirm="Delete this brand? Its products will keep existing without a brand."/></td>
                </tr>
            @empty
                <x-admin.empty colspan="4"/>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $brands->links() }}</div>
@endsection

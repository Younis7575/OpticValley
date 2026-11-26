@extends('admin.layout')
@section('title', 'Categories')
@section('actions')<a href="{{ route('admin.categories.create') }}" class="a-btn-primary">+ Add category</a>@endsection
@section('content')
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[640px]">
        <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($tree as $c)
                @foreach (collect([$c])->merge($c->children) as $row)
                    <tr>
                        <td class="{{ $row->parent_id ? 'pl-10' : 'font-semibold' }}">@if ($row->parent_id)<span class="text-muted">↳</span> @endif{{ $row->name }}</td>
                        <td class="text-muted">{{ $row->slug }}</td>
                        <td>{{ $row->products_count }}</td>
                        <td><x-admin.status :status="$row->is_active ? 'active' : 'inactive'"/> @if (! $row->parent_id && $row->show_in_menu)<span class="a-badge bg-brand-50 text-brand">In menu</span>@endif</td>
                        <td class="space-x-3 text-right"><a href="{{ route('category.show', $row) }}" target="_blank" class="text-xs font-semibold text-muted">View</a><a href="{{ route('admin.categories.edit', $row) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.categories.destroy', $row)"/></td>
                    </tr>
                @endforeach
            @empty
                <x-admin.empty colspan="5"/>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@extends('admin.layout')
@section('title', 'Subcategories')
@section('actions')<a href="{{ route('admin.subcategories.create', array_filter(['parent' => request('parent')])) }}" class="a-btn-primary">+ Add subcategory</a>@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-[230px_1fr]">
    {{-- filter by main category --}}
    <nav class="a-card h-max p-2 text-sm" aria-label="Filter by category">
        <p class="px-3 pb-2 pt-2 text-[11px] font-bold uppercase tracking-wider text-muted">Main category</p>
        <a href="{{ route('admin.subcategories.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2 font-semibold {{ request('parent') ? 'hover:bg-paper' : 'bg-ink text-white' }}">All subcategories</a>
        @foreach ($parents as $id => $name)
            <a href="{{ route('admin.subcategories.index', ['parent' => $id]) }}" class="flex items-center justify-between rounded-lg px-3 py-2 {{ (string) request('parent') === (string) $id ? 'bg-ink font-semibold text-white' : 'hover:bg-paper' }}">{{ $name }}</a>
        @endforeach
        <a href="{{ route('admin.categories.create') }}" class="mt-2 block rounded-lg border border-dashed border-line px-3 py-2 text-center text-xs font-semibold text-muted hover:border-ink hover:text-ink">+ New main category</a>
    </nav>

    <div>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-muted">{{ $subs->total() }} {{ \Illuminate\Support\Str::plural('subcategory', $subs->total()) }}@if (request('parent')) in <strong class="text-ink">{{ $parents[request('parent')] ?? '' }}</strong>@endif</p>
            <x-admin.search placeholder="Search subcategories">@if (request('parent'))<input type="hidden" name="parent" value="{{ request('parent') }}">@endif</x-admin.search>
        </div>
        <div class="a-card overflow-x-auto">
            <table class="a-table min-w-[640px]">
                <thead><tr><th>Subcategory</th><th>Main category</th><th>Products</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($subs as $s)
                        <tr>
                            <td><p class="font-semibold">{{ $s->name }}</p><p class="text-xs text-muted">/category/{{ $s->slug }}</p></td>
                            <td><a href="{{ route('admin.subcategories.index', ['parent' => $s->parent_id]) }}" class="a-badge border border-line bg-paper hover:border-ink">{{ $s->parent?->name }}</a></td>
                            <td>{{ $s->products_count }}</td>
                            <td><x-admin.status :status="$s->is_active ? 'active' : 'inactive'"/></td>
                            <td class="space-x-3 whitespace-nowrap text-right"><a href="{{ route('category.show', $s) }}" target="_blank" class="text-xs font-semibold text-muted">View</a><a href="{{ route('admin.subcategories.edit', $s) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.subcategories.destroy', $s)" :confirm="'Delete the subcategory '.$s->name.'?'"/></td>
                        </tr>
                    @empty
                        <x-admin.empty colspan="5" text="No subcategories here yet. Use “Add subcategory” to create one."/>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $subs->links() }}</div>
    </div>
</div>
@endsection

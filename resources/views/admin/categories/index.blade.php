@extends('admin.layout')
@section('title', 'Categories')
@section('actions')
    <a href="{{ route('admin.subcategories.create') }}" class="a-btn-outline">+ Add subcategory</a>
    <a href="{{ route('admin.categories.create') }}" class="a-btn-primary">+ Add category</a>
@endsection
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="max-w-2xl text-sm text-muted"><strong class="text-ink">Main categories</strong> appear in the store menu (e.g. Eyeglasses, Sunglasses). Put more specific groups such as “Men's Eyeglasses” under them as <a href="{{ route('admin.subcategories.index') }}" class="font-semibold text-brand underline">subcategories</a>.</p>
    <x-admin.search placeholder="Search categories"/>
</div>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($categories as $c)
        <div class="a-card flex flex-col p-5">
            <div class="flex items-start gap-4">
                <div class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-xl bg-paper ring-1 ring-line">
                    @if ($c->image_url)<img src="{{ $c->image_url }}" alt="" class="size-full object-cover">@else<svg class="size-6 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v4H4zM14 15h6v4h-6z"/></svg>@endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2"><p class="truncate text-base font-bold">{{ $c->name }}</p><x-admin.status :status="$c->is_active ? 'active' : 'inactive'"/></div>
                    <p class="text-xs text-muted">/category/{{ $c->slug }} · {{ $c->products_count }} {{ \Illuminate\Support\Str::plural('product', $c->products_count) }} @if ($c->show_in_menu)· in menu @endif</p>
                </div>
            </div>
            <div class="mt-4 flex-1">
                <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-muted">Subcategories ({{ $c->children_count }})</p>
                <div class="flex flex-wrap gap-1.5">
                    @forelse ($c->children as $child)
                        <a href="{{ route('admin.subcategories.edit', $child) }}" class="a-badge border border-line bg-paper hover:border-ink">{{ $child->name }}</a>
                    @empty
                        <span class="text-xs text-muted">None yet.</span>
                    @endforelse
                </div>
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-line pt-4">
                <a href="{{ route('admin.subcategories.create', ['parent' => $c->id]) }}" class="a-btn-primary !px-3 !py-1.5 text-xs">+ Subcategory</a>
                <a href="{{ route('admin.subcategories.index', ['parent' => $c->id]) }}" class="a-btn-outline !px-3 !py-1.5 text-xs">View subcategories</a>
                <a href="{{ route('admin.categories.edit', $c) }}" class="ml-auto text-xs font-semibold hover:underline">Edit</a>
                <a href="{{ route('category.show', $c) }}" target="_blank" class="text-xs font-semibold text-muted">View</a>
                <x-admin.delete :action="route('admin.categories.destroy', $c)" :confirm="'Delete the category '.$c->name.'?'"/>
            </div>
        </div>
    @empty
        <div class="a-card col-span-full p-12 text-center text-sm text-muted">No categories yet. <a href="{{ route('admin.categories.create') }}" class="font-semibold text-ink underline">Create your first category</a>.</div>
    @endforelse
</div>
@endsection

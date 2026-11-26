@extends('admin.layout')
@section('title', 'Navigation menus')
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    @foreach (\App\Models\MenuItem::LOCATIONS as $loc => $label)
        <div class="a-card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4"><h2 class="font-bold">{{ $label }}</h2><a href="{{ route('admin.menus.create', ['location' => $loc]) }}" class="text-xs font-semibold text-brand">+ Add link</a></div>
            <ul class="divide-y divide-line/70">
                @forelse ($groups[$loc] ?? [] as $m)
                    <li class="flex items-center gap-3 px-5 py-3 text-sm"><span class="w-6 text-xs text-muted">{{ $m->sort_order }}</span><div class="min-w-0 flex-1"><p class="font-semibold {{ $m->is_active ? '' : 'text-muted line-through' }}">{{ $m->label }}</p><p class="truncate text-xs text-muted">{{ $m->url }}</p></div><a href="{{ route('admin.menus.edit', $m) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.menus.destroy', $m)" label="×"/></li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-muted">No links.</li>
                @endforelse
            </ul>
        </div>
    @endforeach
</div>
<p class="mt-4 text-xs text-muted">Header links pointing to a category (e.g. /category/eyeglasses) automatically show that category's sub-categories as a dropdown.</p>
@endsection

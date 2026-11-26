@extends('admin.layout')
@section('title', 'Banners')
@section('actions')<a href="{{ route('admin.banners.create') }}" class="a-btn-primary">+ Add banner</a>@endsection
@section('content')
@foreach (\App\Models\Banner::POSITIONS as $pos => $label)
    <div class="mb-8">
        <div class="mb-3 flex items-center justify-between"><h2 class="font-bold">{{ $label }}</h2><a href="{{ route('admin.banners.create', ['position' => $pos]) }}" class="text-xs font-semibold text-brand">+ Add</a></div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($banners[$pos] ?? [] as $b)
                <div class="a-card overflow-hidden">
                    <div class="relative aspect-[16/8] {{ $b->theme === 'dark' ? 'bg-ink' : 'bg-sand' }}">@if ($b->image_url)<img src="{{ $b->image_url }}" alt="" class="size-full object-cover">@endif<span class="absolute left-2 top-2 a-badge bg-white/90">#{{ $b->sort_order }}</span>@unless ($b->is_active)<span class="absolute right-2 top-2 a-badge bg-ink text-white">Hidden</span>@endunless</div>
                    <div class="flex items-start justify-between gap-3 p-4"><div class="min-w-0"><p class="text-[11px] font-bold uppercase tracking-wider text-gold">{{ $b->eyebrow }}</p><p class="truncate font-semibold">{{ $b->title }}</p><p class="truncate text-xs text-muted">{{ $b->link }}</p></div><div class="flex shrink-0 gap-3"><a href="{{ route('admin.banners.edit', $b) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.banners.destroy', $b)"/></div></div>
                </div>
            @empty
                <p class="text-sm text-muted">No banners in this position.</p>
            @endforelse
        </div>
    </div>
@endforeach
@endsection

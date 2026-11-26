@extends('admin.layout')
@section('title', 'Product reviews')
@section('content')
<div class="mb-4 flex gap-1 text-sm">
    @foreach (['' => 'All', 'pending' => 'Pending approval', 'approved' => 'Approved'] as $k => $l)<a href="{{ route('admin.reviews.index', array_filter(['status' => $k])) }}" class="rounded-lg px-3 py-1.5 font-semibold {{ request('status', '') === $k ? 'bg-ink text-white' : 'text-muted hover:bg-white' }}">{{ $l }}</a>@endforeach
</div>
<div class="space-y-3">
    @forelse ($reviews as $r)
        <div class="a-card flex flex-wrap items-start gap-4 p-5">
            <div class="min-w-0 flex-1">
                <p class="text-sm"><span class="text-gold">{{ str_repeat('★', $r->rating) }}<span class="text-line">{{ str_repeat('★', 5 - $r->rating) }}</span></span> <strong class="ml-1">{{ $r->title }}</strong></p>
                <p class="mt-1 text-sm text-ink-2">{{ $r->body }}</p>
                <p class="mt-2 text-xs text-muted">{{ $r->name }} on <a href="{{ $r->product ? route('admin.products.edit', $r->product) : '#' }}" class="underline">{{ $r->product?->name }}</a> · {{ $r->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.reviews.update', $r) }}">@csrf @method('PUT')<input type="hidden" name="is_approved" value="{{ $r->is_approved ? 0 : 1 }}"><button class="{{ $r->is_approved ? 'a-btn-outline' : 'a-btn-primary' }} !py-1.5 text-xs">{{ $r->is_approved ? 'Hide' : 'Approve' }}</button></form>
                <x-admin.delete :action="route('admin.reviews.destroy', $r)"/>
            </div>
        </div>
    @empty
        <div class="a-card p-10 text-center text-sm text-muted">No reviews.</div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection

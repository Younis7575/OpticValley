@extends('admin.layout')
@section('title', 'Blog')
@section('actions')<a href="{{ route('admin.posts.create') }}" class="a-btn-primary">+ New article</a>@endsection
@section('content')
<div class="mb-4"><x-admin.search placeholder="Search articles"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[560px]">
        <thead><tr><th>Article</th><th>Status</th><th>Published</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($posts as $p)<tr><td class="flex items-center gap-3"><img src="{{ $p->image_url ?? asset('images/placeholder.svg') }}" class="h-10 w-16 rounded-lg object-cover" alt=""><span class="font-semibold">{{ $p->title }}</span></td><td>@if ($p->published_at && $p->published_at->isPast())<x-admin.status status="active"/>@elseif ($p->published_at)<span class="a-badge bg-gold/15 text-[#8a6a35]">Scheduled</span>@else<span class="a-badge bg-paper text-muted border border-line">Draft</span>@endif</td><td class="text-muted">{{ $p->published_at?->format('d M Y') ?? '—' }}</td><td class="space-x-3 text-right">@if ($p->published_at && $p->published_at->isPast())<a href="{{ route('blog.show', $p) }}" target="_blank" class="text-xs font-semibold text-muted">View</a>@endif<a href="{{ route('admin.posts.edit', $p) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.posts.destroy', $p)"/></td></tr>@empty<x-admin.empty colspan="4"/>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
@endsection

@extends('admin.layout')
@section('title', 'Pages')
@section('actions')<a href="{{ route('admin.pages.create') }}" class="a-btn-primary">+ Add page</a>@endsection
@section('content')
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[560px]">
        <thead><tr><th>Title</th><th>URL</th><th>Status</th><th>Updated</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($pages as $p)<tr><td class="font-semibold">{{ $p->title }}</td><td class="text-xs text-muted">/pages/{{ $p->slug }}</td><td><x-admin.status :status="$p->is_active ? 'active' : 'inactive'"/></td><td class="text-muted">{{ $p->updated_at->diffForHumans() }}</td><td class="space-x-3 text-right"><a href="{{ route('page.show', $p->slug) }}" target="_blank" class="text-xs font-semibold text-muted">View</a><a href="{{ route('admin.pages.edit', $p) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.pages.destroy', $p)"/></td></tr>@empty<x-admin.empty colspan="5"/>@endforelse
        </tbody>
    </table>
</div>
@endsection

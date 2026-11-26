@extends('admin.layout')
@section('title', 'Activity log')
@section('content')
<div class="mb-4"><x-admin.search placeholder="Search activity"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[640px]">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
            @forelse ($logs as $l)<tr><td class="whitespace-nowrap text-muted">{{ $l->created_at->format('d M, g:i a') }}</td><td>{{ $l->user?->name ?? 'System' }}</td><td><span class="a-badge bg-paper border border-line font-mono">{{ $l->action }}</span></td><td>{{ $l->description }}</td><td class="text-xs text-muted">{{ $l->ip }}</td></tr>@empty<x-admin.empty colspan="5" text="No activity recorded yet."/>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

@extends('admin.layout')
@section('title', 'Newsletter subscribers')
@section('actions')<a href="{{ route('admin.subscribers.export') }}" class="a-btn-outline">Export CSV</a>@endsection
@section('content')
<div class="mb-4"><x-admin.search placeholder="Search email"/></div>
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[480px]">
        <thead><tr><th>Email</th><th>Subscribed</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($subscribers as $s)<tr><td class="font-medium">{{ $s->email }}</td><td class="text-muted">{{ $s->created_at->format('d M Y') }}</td><td class="text-right"><x-admin.delete :action="route('admin.subscribers.destroy', $s)" label="Remove"/></td></tr>@empty<x-admin.empty colspan="3" text="No subscribers yet."/>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection

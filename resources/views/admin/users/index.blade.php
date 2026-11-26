@extends('admin.layout')
@section('title', 'Admin users')
@section('actions')<a href="{{ route('admin.users.create') }}" class="a-btn-primary">+ Add admin user</a>@endsection
@section('content')
<div class="a-card overflow-x-auto">
    <table class="a-table min-w-[620px]">
        <thead><tr><th>User</th><th>Role</th><th>Last login</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @foreach ($users as $u)<tr><td><p class="font-semibold">{{ $u->name }} @if ($u->is(auth()->user()))<span class="a-badge bg-paper text-muted">You</span>@endif</p><p class="text-xs text-muted">{{ $u->email }}</p></td><td>{{ $u->role?->name }}</td><td class="text-muted">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td><td><x-admin.status :status="$u->is_active ? 'active' : 'inactive'"/></td><td class="space-x-3 text-right"><a href="{{ route('admin.users.edit', $u) }}" class="text-xs font-semibold hover:underline">Edit</a>@unless ($u->is(auth()->user()))<x-admin.delete :action="route('admin.users.destroy', $u)"/>@endunless</td></tr>@endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('admin.layout')
@section('title', 'Roles & permissions')
@section('actions')<a href="{{ route('admin.roles.create') }}" class="a-btn-primary">+ Add role</a>@endsection
@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($roles as $r)
        <div class="a-card p-5">
            <div class="flex items-start justify-between"><div><p class="font-bold">{{ $r->name }}</p><p class="text-xs text-muted">{{ $r->users_count }} {{ \Illuminate\Support\Str::plural('user', $r->users_count) }}</p></div>@if ($r->slug !== 'super-admin')<div class="flex gap-3"><a href="{{ route('admin.roles.edit', $r) }}" class="text-xs font-semibold hover:underline">Edit</a><x-admin.delete :action="route('admin.roles.destroy', $r)"/></div>@endif</div>
            <div class="mt-4 flex flex-wrap gap-1.5">
                @if (in_array('*', $r->permissions ?? []))<span class="a-badge bg-brand text-white">Full access</span>@else @foreach ($r->permissions ?? [] as $p)<span class="a-badge bg-paper border border-line">{{ ucfirst($p) }}</span>@endforeach @endif
            </div>
        </div>
    @endforeach
</div>
@endsection

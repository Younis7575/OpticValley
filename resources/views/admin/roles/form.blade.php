@extends('admin.layout')
@section('title', $role->exists ? 'Edit role' : 'Add role')
@section('content')
<form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="a-card max-w-xl space-y-5 p-6">
    @csrf @if ($role->exists) @method('PUT') @endif
    <x-admin.field name="name" label="Role name" :value="$role->name" required/>
    <div><span class="a-label">Permissions</span>
        <div class="space-y-2 rounded-xl border border-line p-4">
            @foreach (\App\Models\Role::PERMISSIONS as $k => $l)<label class="flex items-start gap-3 text-sm"><input type="checkbox" name="permissions[]" value="{{ $k }}" @checked(in_array($k, old('permissions', $role->permissions ?? []))) class="mt-1"><span><span class="font-semibold">{{ ucfirst($k) }}</span><span class="block text-xs text-muted">{{ $l }}</span></span></label>@endforeach
        </div>
    </div>
    <button class="a-btn-primary">Save role</button>
</form>
@endsection

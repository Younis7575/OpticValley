@extends('admin.layout')
@section('title', $user->exists ? 'Edit admin user' : 'Add admin user')
@section('content')
<form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="a-card max-w-xl space-y-4 p-6">
    @csrf @if ($user->exists) @method('PUT') @endif
    <x-admin.field name="name" label="Name" :value="$user->name" required/>
    <x-admin.field name="email" type="email" label="Email" :value="$user->email" required/>
    <x-admin.field name="role_id" type="select" label="Role" :value="$user->role_id" :options="$roles"/>
    <x-admin.field name="password" type="password" :label="$user->exists ? 'New password (leave empty to keep)' : 'Password'" :required="! $user->exists" help="At least 10 characters with letters and numbers." autocomplete="new-password"/>
    <x-admin.toggle name="is_active" label="Active" :checked="$user->is_active"/>
    <button class="a-btn-primary">Save</button>
</form>
@endsection

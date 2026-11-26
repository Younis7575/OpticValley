@extends('admin.layout')
@section('title', 'My profile')
@section('content')
<form method="POST" action="{{ route('admin.profile.update') }}" class="a-card max-w-xl space-y-4 p-6">
    @csrf @method('PUT')
    <x-admin.field name="name" label="Name" :value="$user->name" required/>
    <x-admin.field name="email" type="email" label="Email" :value="$user->email" required/>
    <hr class="border-line">
    <p class="text-sm font-semibold">Change password <span class="font-normal text-muted">(optional)</span></p>
    <x-admin.field name="current_password" type="password" label="Current password" autocomplete="current-password"/>
    <x-admin.field name="password" type="password" label="New password" help="At least 10 characters with letters and numbers." autocomplete="new-password"/>
    <x-admin.field name="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password"/>
    <button class="a-btn-primary">Save</button>
</form>
@endsection

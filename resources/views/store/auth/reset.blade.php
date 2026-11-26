@extends('layouts.store')
@section('title', 'Choose a new password')
@section('noindex', true)
@section('content')
@component('store.auth._shell', ['heading' => 'Choose a new password'])
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="password">New password</label><input id="password" type="password" name="password" required autocomplete="new-password" class="field">@error('password')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required class="field"></div>
        <button class="btn-primary w-full !py-3.5">Reset password</button>
    </form>
@endcomponent
@endsection

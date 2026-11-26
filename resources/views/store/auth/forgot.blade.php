@extends('layouts.store')
@section('title', 'Reset password')
@section('noindex', true)
@section('content')
@component('store.auth._shell', ['heading' => 'Forgot password?', 'sub' => "Enter your email and we'll send you a reset link."])
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <button class="btn-primary w-full !py-3.5">Send reset link</button>
        <a href="{{ route('login') }}" class="block text-center text-xs font-semibold text-muted hover:text-ink">Back to sign in</a>
    </form>
@endcomponent
@endsection

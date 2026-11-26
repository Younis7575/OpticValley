@extends('layouts.store')
@section('title', 'Sign in')
@section('noindex', true)
@section('content')
@component('store.auth._shell', ['heading' => 'Welcome back', 'sub' => 'New here? <a href="'.route('register').'" class="font-semibold text-ink underline underline-offset-4">Create an account</a>'])
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><div class="flex justify-between"><label class="label" for="password">Password</label><a href="{{ route('password.request') }}" class="text-xs font-semibold text-muted hover:text-ink">Forgot?</a></div><input id="password" type="password" name="password" required autocomplete="current-password" class="field"></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" class="accent-ink"> Keep me signed in</label>
        <button class="btn-primary w-full !py-3.5">Sign in</button>
    </form>
@endcomponent
@endsection

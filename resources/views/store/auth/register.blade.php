@extends('layouts.store')
@section('title', 'Create account')
@section('noindex', true)
@section('content')
@component('store.auth._shell', ['heading' => 'Create your account', 'sub' => 'Already have one? <a href="'.route('login').'" class="font-semibold text-ink underline underline-offset-4">Sign in</a>'])
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div><label class="label" for="name">Full name</label><input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="field">@error('name')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="phone">Mobile (optional)</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="field">@error('phone')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="password">Password</label><input id="password" type="password" name="password" required autocomplete="new-password" class="field"><p class="mt-1 text-[11px] text-muted">At least 8 characters with letters and numbers.</p>@error('password')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <div><label class="label" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field"></div>
        <button class="btn-primary w-full !py-3.5">Create account</button>
    </form>
@endcomponent
@endsection

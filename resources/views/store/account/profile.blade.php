@extends('store.account._layout')
@section('title', 'Profile')
@section('heading', 'Profile & password')
@section('account')
    <div class="grid gap-6 xl:grid-cols-2">
        <form method="POST" action="{{ route('account.profile.update') }}" class="card space-y-4 p-6">
            @csrf @method('PUT')
            <h2 class="text-xl">Personal details</h2>
            <div><label class="label">Full name</label><input name="name" value="{{ old('name', $user->name) }}" required class="field">@error('name')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label">Mobile</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="field">@error('phone')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <button class="btn-primary">Save changes</button>
        </form>
        <form method="POST" action="{{ route('account.password') }}" class="card space-y-4 p-6">
            @csrf @method('PUT')
            <h2 class="text-xl">Change password</h2>
            <div><label class="label">Current password</label><input type="password" name="current_password" required class="field" autocomplete="current-password">@error('current_password')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label">New password</label><input type="password" name="password" required class="field" autocomplete="new-password">@error('password')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label">Confirm new password</label><input type="password" name="password_confirmation" required class="field" autocomplete="new-password"></div>
            <button class="btn-primary">Update password</button>
        </form>
    </div>
@endsection

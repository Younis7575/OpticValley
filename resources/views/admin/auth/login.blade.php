<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">
    <title>Admin sign in · {{ setting('store_name', 'OpticValley') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/favicon-192.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="grid min-h-screen lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-ink p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="pointer-events-none absolute -right-40 -top-40 size-[600px] rounded-full border border-white/10"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-20 size-[420px] rounded-full bg-white/5"></div>
        <div class="relative"><img src="{{ asset('images/logo-light.png') }}" alt="OpticValley" class="h-24 w-auto"></div>
        <div class="relative">
            <p class="font-display text-5xl leading-tight">Run your store<br>with <em class="text-gold">clarity</em>.</p>
            <p class="mt-4 max-w-sm text-white/60">Orders, inventory, customers and content — all in one place.</p>
        </div>
        <p class="relative text-xs text-white/40">© {{ date('Y') }} {{ setting('store_name', 'OpticValley') }}</p>
    </div>
    <div class="grid place-items-center p-6">
        <form method="POST" action="{{ route('admin.login') }}" class="w-full max-w-sm space-y-5">
            @csrf
            <div><h1 class="font-display text-3xl">Admin sign in</h1><p class="mt-1 text-sm text-muted">Authorised staff only.</p></div>
            <div><label class="a-label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="a-input !py-3">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="a-label" for="password">Password</label><input id="password" type="password" name="password" required class="a-input !py-3"></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="a-btn-primary w-full !py-3">Sign in</button>
        </form>
    </div>
</body>
</html>

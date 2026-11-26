@extends('layouts.store')
@section('noindex', true)
@section('content')
<section class="container-x py-12 md:py-16">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">My account</p><h1 class="mt-1 text-4xl">@yield('heading')</h1></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-ghost !px-4 text-muted"><x-icon name="logout" class="size-4"/> Sign out</button></form>
    </div>
    <div class="mt-10 grid gap-8 lg:grid-cols-[230px_1fr]">
        <nav class="no-scrollbar -mx-4 flex gap-1 overflow-x-auto px-4 lg:mx-0 lg:flex-col lg:px-0" aria-label="Account">
            @foreach ([['account.dashboard', 'Overview', 'user'], ['account.orders', 'Orders', 'box'], ['account.wishlist', 'Wishlist', 'heart'], ['account.addresses', 'Addresses', 'pin'], ['account.profile', 'Profile & password', 'shield']] as [$r, $l, $i])
                <a href="{{ route($r) }}" class="flex shrink-0 items-center gap-3 rounded-full px-4 py-2.5 text-sm font-semibold transition lg:rounded-2xl {{ request()->routeIs($r.'*') && ($r !== 'account.dashboard' || request()->routeIs('account.dashboard')) ? 'bg-ink text-white' : 'text-ink-2 hover:bg-sand' }}"><x-icon :name="$i" class="size-4"/> {{ $l }}</a>
            @endforeach
        </nav>
        <div class="min-w-0">@yield('account')</div>
    </div>
</section>
@endsection

@php
    $u = auth()->user();
    $nav = [
        ['Overview', [
            ['admin.dashboard', 'Dashboard', 'dashboard', 'M3 12h7V3H3v9Zm0 9h7v-6H3v6Zm11 0h7v-9h-7v9Zm0-18v6h7V3h-7Z'],
        ]],
        ['Catalogue', [
            ['admin.products.index', 'Products', 'products', 'm3 7 9-4 9 4v10l-9 4-9-4V7Zm0 0 9 4 9-4M12 11v10'],
            ['admin.categories.index', 'Categories', 'products', 'M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v4H4zM14 15h6v4h-6z'],
            ['admin.subcategories.index', 'Subcategories', 'products', 'M5 4v12a3 3 0 0 0 3 3h11M9 9h10M9 14h10'],
            ['admin.brands.index', 'Brands', 'products', 'M3 12V4h8l10 10-8 8L3 12Z'],
            ['admin.lens-options.index', 'Lens options', 'products', 'M7 12a5 5 0 1 0 10 0 5 5 0 0 0-10 0Z'],
        ]],
        ['Sales', [
            ['admin.orders.index', 'Orders', 'orders', 'M5 8h14l-1 12H6L5 8Zm4 0V6a3 3 0 0 1 6 0v2'],
            ['admin.customers.index', 'Customers', 'customers', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-8 9a8 8 0 0 1 16 0'],
        ]],
        ['Marketing', [
            ['admin.coupons.index', 'Coupons', 'marketing', 'M4 8V5h16v3a3 3 0 0 0 0 6v3H4v-3a3 3 0 0 0 0-6Z'],
            ['admin.banners.index', 'Banners', 'marketing', 'M3 5h18v14H3zM3 15l5-5 4 4 3-3 6 6'],
            ['admin.subscribers.index', 'Subscribers', 'marketing', 'M3 5h18v14H3zM3 7l9 6 9-6'],
        ]],
        ['Content', [
            ['admin.pages.index', 'Pages', 'content', 'M6 3h9l4 4v14H6zM14 3v5h5'],
            ['admin.posts.index', 'Blog', 'content', 'M4 20h16M6 16l10-10 3 3-10 10H6v-3Z'],
            ['admin.menus.index', 'Menus', 'content', 'M4 6h16M4 12h16M4 18h10'],
            ['admin.reviews.index', 'Reviews', 'content', 'm12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9L12 3Z'],
            ['admin.messages.index', 'Messages', 'content', 'M4 5h16v11H8l-4 4V5Z'],
        ]],
        ['System', [
            ['admin.settings.edit', 'Settings', 'settings', 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.4-3a7.4 7.4 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 0 0-2-1.2L14.5 3h-4l-.4 2.6a7 7 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.4 7.4 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7 7 0 0 0 2 1.2l.4 2.6h4l.4-2.6a7 7 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z'],
            ['admin.users.index', 'Admin users', 'users', 'M16 11a3 3 0 1 0 0-6M9 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0m2 0a6 6 0 0 0-3-5'],
            ['admin.roles.index', 'Roles', 'users', 'M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z'],
            ['admin.activity.index', 'Activity log', 'logs', 'M12 7v5l3 2M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z'],
        ]],
    ];
    $pendingOrders = $u->hasPermission('orders') ? \App\Models\Order::where('status', 'pending')->count() : 0;
    $unread = $u->hasPermission('content') ? \App\Models\ContactMessage::where('is_read', false)->count() : 0;
    $pendingReviews = $u->hasPermission('content') ? \App\Models\Review::where('is_approved', false)->count() : 0;
    $badges = ['admin.orders.index' => $pendingOrders, 'admin.messages.index' => $unread, 'admin.reviews.index' => $pendingReviews];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ setting('store_name', 'OpticValley') }} Admin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/favicon-192.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <script>window.__flash = @json(collect(['success', 'error'])->filter(fn ($t) => session($t))->map(fn ($t) => ['type' => $t, 'message' => session($t)])->values());</script>
</head>
<body x-data="{ side: false }">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <div x-show="side" x-cloak @click="side = false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-ink text-white/70 transition lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" :class="side && '!translate-x-0'">
        <div class="flex h-16 items-center gap-2.5 px-5">
            <img src="{{ asset('images/logo-mark-light.png') }}" alt="" class="h-7 w-auto"><span class="font-display text-sm tracking-[0.22em] text-white">OPTIC <span class="text-gold">VALLEY</span></span>
            <span class="ml-auto rounded bg-white/10 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">Admin</span>
        </div>
        <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4 text-sm">
            @foreach ($nav as [$group, $items])
                @php $visible = collect($items)->filter(fn ($i) => $u->hasPermission($i[2])); @endphp
                @if ($visible->isNotEmpty())
                    <div>
                        <p class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-white/35">{{ $group }}</p>
                        @foreach ($visible as [$route, $label, $perm, $icon])
                            @php $active = request()->routeIs(\Illuminate\Support\Str::beforeLast($route, '.').'.*') || request()->routeIs($route); @endphp
                            <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium transition {{ $active ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                                <svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
                                {{ $label }}
                                @if (($badges[$route] ?? 0) > 0)<span class="ml-auto rounded-full bg-gold px-1.5 text-[10px] font-bold text-ink">{{ $badges[$route] }}</span>@endif
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </nav>
        <a href="{{ route('home') }}" target="_blank" class="mx-3 mb-3 flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2.5 text-xs font-semibold hover:bg-white/5 hover:text-white">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 4h6v6M20 4l-9 9M18 14v6H4V6h6"/></svg> View storefront
        </a>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-white/85 px-4 backdrop-blur md:px-8">
            <button @click="side = true" class="grid size-9 place-items-center rounded-lg hover:bg-paper lg:hidden" aria-label="Menu"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
            <div class="min-w-0">
                <h1 class="truncate text-lg font-bold">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="ml-auto flex items-center gap-2">
                @yield('actions')
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-paper">
                        <span class="grid size-8 place-items-center rounded-full bg-brand text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                        <span class="hidden text-left text-xs sm:block"><span class="block font-semibold">{{ $u->name }}</span><span class="text-muted">{{ $u->role?->name }}</span></span>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 rounded-xl border border-line bg-white p-1.5 text-sm shadow-xl">
                        <a href="{{ route('admin.profile') }}" class="block rounded-lg px-3 py-2 hover:bg-paper">My profile</a>
                        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="block w-full rounded-lg px-3 py-2 text-left text-sale hover:bg-sale/5">Sign out</button></form>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 p-4 md:p-8">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-sale/20 bg-sale/5 px-4 py-3 text-sm text-sale">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<div x-data="toasts" class="pointer-events-none fixed bottom-5 right-5 z-50 flex flex-col items-end gap-2">
    <template x-for="t in items" :key="t.id">
        <div x-transition class="pointer-events-auto rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-xl" :class="t.type === 'error' ? 'bg-sale' : 'bg-ink'" x-text="t.message"></div>
    </template>
</div>
@stack('scripts')
</body>
</html>

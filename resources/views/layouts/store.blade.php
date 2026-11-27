@php
    $storeName = setting('store_name', 'OpticValley');
    $metaTitle = trim($__env->yieldContent('title')) ? trim($__env->yieldContent('title')).' | '.$storeName : setting('meta_title', $storeName);
    $metaDesc = trim($__env->yieldContent('description')) ?: setting('meta_description');
    $ogImage = trim($__env->yieldContent('og_image')) ?: asset('uploads/demo/hero-1.svg');
    $wa = preg_replace('/\D/', '', setting('whatsapp_number', ''));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count" content="{{ $cartCount }}">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 160) }}">
    <link rel="canonical" href="{{ trim($__env->yieldContent('canonical')) ?: url()->current() }}">
    @hasSection('noindex')<meta name="robots" content="noindex, follow">@endif
    <meta property="og:site_name" content="{{ $storeName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 200) }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/favicon-192.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $storeName, 'url' => url('/'),
        'logo' => asset('images/logo.png'), 'email' => setting('store_email'), 'telephone' => setting('store_phone'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('store_address'), 'addressCountry' => 'PK'],
        'sameAs' => array_values(array_filter([setting('social_instagram'), setting('social_facebook'), setting('social_tiktok'), setting('social_youtube')])),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('head')
    @if (setting('ga4_measurement_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('ga4_measurement_id') }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',@js(setting('ga4_measurement_id')),{anonymize_ip:true});</script>
    @endif
    <script>window.__flash = @json(collect(['success', 'error'])->filter(fn ($t) => session($t))->map(fn ($t) => ['type' => $t, 'message' => session($t)])->values());</script>
</head>
<body class="min-h-screen" x-data="{ menu: false, search: false }" @keydown.escape.window="menu = false; search = false">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-ink focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

    {{-- Announcement --}}
    @if (setting('announcement'))
        <div class="overflow-hidden bg-onyx text-[11px] font-semibold uppercase tracking-[0.24em] text-gold-light/90">
            <div class="flex w-max animate-marquee gap-16 py-2.5">
                @for ($i = 0; $i < 8; $i++)<span class="flex items-center gap-16">{{ setting('announcement') }} <span class="text-gold">◆</span></span>@endfor
            </div>
        </div>
    @endif

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-line/70 bg-white/90 backdrop-blur-xl" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 10" :class="scrolled && 'shadow-[0_8px_30px_-12px_rgba(19,23,28,.12)]'">
        <div class="container-x flex h-[72px] items-center gap-4">
            <button class="-ml-2 grid size-10 place-items-center rounded-full hover:bg-sand lg:hidden" @click="menu = true" aria-label="Open menu"><x-icon name="menu"/></button>
            <x-logo class="max-lg:mx-auto"/>

            <nav class="ml-8 hidden h-full items-center gap-0.5 xl:ml-12 lg:flex" aria-label="Main">
                @php
                    $catBySlug = $navCategories->keyBy('slug');
                    $headerItems = ($menus['header'] ?? collect())->map(function ($m) use ($catBySlug) {
                        $slug = \Illuminate\Support\Str::after(parse_url($m->url, PHP_URL_PATH) ?? '', '/category/');
                        return (object) ['label' => $m->label, 'url' => $m->url, 'cat' => str_contains($m->url, '/category/') ? $catBySlug->get($slug) : null, 'sale' => str_contains($m->url, 'sale=1')];
                    });
                @endphp
                @foreach ($headerItems as $item)
                    @php $cat = $item->cat; @endphp
                    <div class="group relative flex h-full items-center">
                        <a href="{{ $item->url }}" class="flex items-center gap-1 whitespace-nowrap rounded-full px-3 py-2 text-[13px] font-semibold transition {{ $item->sale ? 'text-sale' : 'text-ink-2 hover:text-ink' }}">
                            {{ $item->label }} @if ($cat && $cat->children->isNotEmpty())<x-icon name="chevron-down" class="size-3.5 opacity-50 transition group-hover:rotate-180"/>@endif
                        </a>
                        @if ($cat && $cat->children->isNotEmpty())
                            <div class="invisible absolute left-1/2 top-full w-[440px] -translate-x-1/2 translate-y-2 opacity-0 transition duration-300 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                                <div class="mt-0 grid grid-cols-[1fr_170px] gap-6 rounded-3xl border border-line bg-white p-6 shadow-2xl shadow-ink/10">
                                    <ul class="space-y-1">
                                        <li><a href="{{ route('category.show', $cat) }}" class="block rounded-xl px-3 py-2 text-sm font-semibold hover:bg-sand">All {{ $cat->name }}</a></li>
                                        @foreach ($cat->children as $child)
                                            <li><a href="{{ route('category.show', $child) }}" class="block rounded-xl px-3 py-2 text-sm text-ink-2 hover:bg-sand hover:text-ink">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('category.show', $cat) }}" class="relative overflow-hidden rounded-2xl bg-sand p-4">
                                        <p class="eyebrow">Explore</p>
                                        <p class="mt-1 font-display text-lg leading-tight">{{ $cat->name }}</p>
                                        <svg viewBox="0 0 64 40" class="absolute -bottom-2 -right-3 h-20 text-ink/10" fill="none" stroke="currentColor" stroke-width="4"><circle cx="16" cy="22" r="11"/><circle cx="48" cy="22" r="11"/><path d="M27 20q5-5 10 0"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

            </nav>

            <div class="ml-auto flex items-center gap-0.5 max-lg:ml-0">
                <button class="grid size-10 place-items-center rounded-full hover:bg-sand" @click="search = true; $nextTick(() => $refs.q.focus())" aria-label="Search"><x-icon name="search"/></button>
                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" class="hidden size-10 place-items-center rounded-full hover:bg-sand sm:grid" aria-label="Account"><x-icon name="user"/></a>
                <a href="{{ route('account.wishlist') }}" class="hidden size-10 place-items-center rounded-full hover:bg-sand sm:grid" aria-label="Wishlist"><x-icon name="heart"/></a>
                <a href="{{ route('cart.index') }}" class="relative grid size-10 place-items-center rounded-full hover:bg-sand" aria-label="Cart">
                    <x-icon name="bag"/>
                    <span x-cloak x-show="$store.cart.count > 0" x-text="$store.cart.count" class="absolute right-0.5 top-0.5 grid min-w-[18px] place-items-center rounded-full gold-grad px-1 text-[10px] font-bold leading-[18px] text-ink"></span>
                </a>
            </div>
        </div>
    </header>

    {{-- Search overlay --}}
    <div x-cloak x-show="search" x-transition.opacity class="fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm" @click.self="search = false">
        <div x-show="search" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-y-6 opacity-0" class="bg-paper shadow-2xl">
            <form action="{{ route('shop') }}" class="container-x relative flex items-center gap-4 py-6" x-data="searchSuggest" @submit="window.ovTrack && ovTrack('search', { search_term: q })">
                <x-icon name="search" class="size-6 text-muted"/>
                <input x-ref="q" name="q" type="search" x-model="q" @input="fetch()" role="combobox" aria-autocomplete="list" :aria-expanded="items.length > 0" placeholder="Search frames, sunglasses, brands…" class="w-full bg-transparent font-display text-2xl outline-none placeholder:text-muted/60 md:text-3xl" autocomplete="off">
                <button type="button" @click="search = false" class="grid size-10 shrink-0 place-items-center rounded-full hover:bg-sand" aria-label="Close search"><x-icon name="x"/></button>
                <ul x-show="items.length" x-cloak class="absolute inset-x-4 top-full z-10 overflow-hidden rounded-2xl border border-line bg-white shadow-2xl sm:inset-x-6 lg:inset-x-10" role="listbox">
                    <template x-for="it in items" :key="it.url">
                        <li role="option"><a :href="it.url" class="flex items-center gap-4 px-4 py-3 hover:bg-[#f7f6f2]">
                            <img :src="it.image" alt="" class="h-12 w-16 rounded-lg object-contain ring-1 ring-line">
                            <span class="flex-1"><span class="block text-sm font-semibold" x-text="it.name"></span><span class="text-xs text-muted" x-text="it.brand"></span></span>
                            <span class="text-sm font-bold" x-text="it.price"></span>
                        </a></li>
                    </template>
                </ul>
            </form>
            <div class="container-x flex flex-wrap gap-2 pb-6">
                <span class="mr-2 self-center text-xs font-semibold uppercase tracking-widest text-muted">Popular</span>
                @foreach (['Aviator', 'Cat-eye', 'Blue light', 'Round', 'Kids', 'Polarised'] as $term)
                    <a href="{{ route('shop', ['q' => $term]) }}" class="chip">{{ $term }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div x-cloak x-show="menu" class="fixed inset-0 z-50 lg:hidden">
        <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-ink/40" @click="menu = false"></div>
        <aside x-show="menu" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-x-full" x-transition:leave="transition duration-200" x-transition:leave-end="-translate-x-full"
               class="absolute inset-y-0 left-0 flex w-[86%] max-w-sm flex-col overflow-y-auto bg-paper">
            <div class="flex items-center justify-between border-b border-line p-5"><x-logo/><button @click="menu = false" class="grid size-10 place-items-center rounded-full hover:bg-sand" aria-label="Close menu"><x-icon name="x"/></button></div>
            <nav class="flex-1 p-3">
                @foreach ($navCategories as $cat)
                    <div x-data="{ open: false }" class="border-b border-line/70">
                        <div class="flex items-center">
                            <a href="{{ route('category.show', $cat) }}" class="flex-1 px-3 py-3.5 font-semibold">{{ $cat->name }}</a>
                            @if ($cat->children->isNotEmpty())<button @click="open = !open" class="grid size-10 place-items-center" aria-label="Expand"><x-icon name="chevron-down" class="size-4 transition" ::class="open && 'rotate-180'"/></button>@endif
                        </div>
                        @if ($cat->children->isNotEmpty())
                            <div x-show="open" x-collapse class="pb-2 pl-3">
                                @foreach ($cat->children as $child)<a href="{{ route('category.show', $child) }}" class="block px-3 py-2 text-sm text-ink-2">{{ $child->name }}</a>@endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <a href="{{ route('shop', ['sale' => 1]) }}" class="block border-b border-line/70 px-3 py-3.5 font-semibold text-sale">Sale</a>
                <a href="{{ route('blog.index') }}" class="block px-3 py-3.5 font-semibold">Journal</a>
            </nav>
            <div class="grid grid-cols-2 gap-2 border-t border-line p-4">
                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" class="btn-outline !py-2.5"><x-icon name="user" class="size-4"/> Account</a>
                <a href="{{ route('account.wishlist') }}" class="btn-outline !py-2.5"><x-icon name="heart" class="size-4"/> Wishlist</a>
            </div>
        </aside>
    </div>

    <main id="main">
        @yield('content')
    </main>

    {{-- Trust bar --}}
    <section class="border-t border-line bg-white">
        <div class="container-x grid grid-cols-2 gap-6 py-10 md:grid-cols-4">
            @foreach ([['truck', 'Nationwide delivery', setting('shipping_note', 'Delivery in 2–5 working days.')], ['shield', 'Optician checked', 'Every lens verified before dispatch.'], ['refresh', '7-day returns', 'Easy returns on unused frames.'], ['tag', 'Cash on Delivery', 'Pay at your doorstep, anywhere in Pakistan.']] as [$ic, $t, $d])
                <div class="flex items-start gap-3.5">
                    <span class="grid size-11 shrink-0 place-items-center rounded-full border border-[#b8914f]/30 bg-brand-50 text-brand"><x-icon :name="$ic"/></span>
                    <div><p class="text-sm font-bold">{{ $t }}</p><p class="mt-0.5 text-xs leading-relaxed text-muted">{{ $d }}</p></div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative bg-onyx text-white/65"><div class="h-px gold-grad"></div>
        <div class="container-x grid gap-12 py-16 lg:grid-cols-[1.3fr_1fr_1fr_1.3fr]">
            <div>
                <x-logo light full class="-ml-2"/>
                <p class="mt-5 max-w-xs text-sm leading-relaxed">{{ setting('footer_about') }}</p>
                <div class="mt-6 flex gap-2">
                    @foreach (['instagram', 'facebook', 'tiktok', 'youtube'] as $s)
                        @if (setting('social_'.$s))
                            <a href="{{ setting('social_'.$s) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-white/15 transition hover:border-[#b8914f] hover:text-gold-light" aria-label="{{ ucfirst($s) }}"><x-icon :name="$s" class="size-[18px]"/></a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div>
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.24em] text-gold-light">Shop</p>
                <ul class="space-y-2.5 text-sm">@foreach ($menus['footer_shop'] ?? [] as $m)<li><a href="{{ $m->url }}" class="link-u hover:text-white">{{ $m->label }}</a></li>@endforeach</ul>
            </div>
            <div>
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.24em] text-gold-light">Help</p>
                <ul class="space-y-2.5 text-sm">@foreach ($menus['footer_help'] ?? [] as $m)<li><a href="{{ $m->url }}" class="link-u hover:text-white">{{ $m->label }}</a></li>@endforeach</ul>
            </div>
            <div>
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.24em] text-gold-light">Join the list</p>
                <p class="text-sm">New drops, early sale access and eye-care tips. No spam.</p>
                <form action="{{ route('newsletter') }}" method="POST" class="mt-4 flex rounded-full border border-white/20 p-1 focus-within:border-white">
                    @csrf
                    <input type="email" name="email" required placeholder="Your email" class="w-full bg-transparent px-4 text-sm text-white outline-none placeholder:text-white/40" aria-label="Email">
                    <button class="gold-grad rounded-full px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-ink transition hover:brightness-110">Join</button>
                </form>
                <ul class="mt-6 space-y-2 text-sm">
                    <li class="flex gap-2.5"><x-icon name="pin" class="mt-0.5 size-4 shrink-0"/> {{ setting('store_address') }}</li>
                    <li class="flex gap-2.5"><x-icon name="phone" class="size-4 shrink-0"/> <a href="tel:{{ preg_replace('/\s/', '', setting('store_phone')) }}" class="hover:text-white">{{ setting('store_phone') }}</a></li>
                    <li class="flex gap-2.5"><x-icon name="mail" class="size-4 shrink-0"/> <a href="mailto:{{ setting('store_email') }}" class="hover:text-white">{{ setting('store_email') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-x flex flex-col items-center justify-between gap-3 py-6 text-xs md:flex-row">
                <p>© {{ date('Y') }} {{ $storeName }}. All rights reserved.</p>
                <div class="flex flex-wrap justify-center gap-5">
                    <a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-white">Privacy</a>
                    <a href="{{ route('page.show', 'terms-conditions') }}" class="hover:text-white">Terms</a>
                    <a href="{{ route('page.show', 'about-us') }}" class="hover:text-white">About</a>
                    <a href="{{ route('blog.index') }}" class="hover:text-white">Journal</a>
                </div>
            </div>
        </div>
    </footer>

    @if ($wa)
        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hi '.$storeName.', I have a question.') }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-[#25D366]/30 transition hover:scale-110" aria-label="Chat on WhatsApp">
            <svg viewBox="0 0 32 32" class="size-7" fill="currentColor"><path d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3Zm0 23.7c-2 0-4-.6-5.7-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.7 10.7 0 1 1 16 26.7Zm5.9-8c-.3-.2-1.9-1-2.2-1s-.5-.2-.7.1-.8 1-1 1.2-.4.2-.7 0a8.8 8.8 0 0 1-4.4-3.8c-.3-.6.3-.5 1-1.7.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 0 0-.9.4 3.7 3.7 0 0 0-1.1 2.7 6.4 6.4 0 0 0 1.3 3.4 14.7 14.7 0 0 0 5.7 5c2.1.9 2.9 1 4 .8.6-.1 1.9-.8 2.2-1.5s.3-1.4.2-1.5-.3-.3-.6-.4Z"/></svg>
        </a>
    @endif

    {{-- Quick view --}}
    <div x-data="quickView" @quickview.window="open($event.detail)" @keydown.escape.window="close()">
        <div x-cloak x-show="show" x-transition.opacity class="fixed inset-0 z-[65] flex items-end justify-center bg-[#0e0d0c]/60 backdrop-blur-sm sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-label="Quick view">
            <div class="relative max-h-[92dvh] w-full max-w-4xl overflow-y-auto bg-white sm:rounded-[1.75rem]" @click.outside="close()">
                <button type="button" @click="close()" class="absolute right-4 top-4 z-10 grid size-10 place-items-center rounded-full bg-white shadow hover:bg-sand" aria-label="Close quick view"><x-icon name="x"/></button>
                <div x-show="loading" class="grid h-72 place-items-center text-sm text-muted">Loading…</div>
                <div x-html="html"></div>
            </div>
        </div>
    </div>

    {{-- Toasts --}}
    <div x-data="toasts" class="pointer-events-none fixed inset-x-0 top-20 z-[60] flex flex-col items-center gap-2 px-4">
        <template x-for="t in items" :key="t.id">
            <div x-transition:enter="transition duration-300" x-transition:enter-start="-translate-y-3 opacity-0" class="pointer-events-auto flex items-center gap-3 rounded-full px-5 py-3 text-sm font-semibold shadow-xl"
                 :class="t.type === 'error' ? 'bg-sale text-white' : 'bg-onyx text-white ring-1 ring-[#b8914f]/40'">
                <span x-text="t.message"></span>
                <a x-show="t.type !== 'error' && t.message.includes('cart')" href="{{ route('cart.index') }}" class="underline underline-offset-4">View cart</a>
            </div>
        </template>
    </div>
    @stack('scripts')
</body>
</html>

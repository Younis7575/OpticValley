@extends('layouts.store')

@php
    $h = $heroCopy;
    $shapeSvg = [
        'round' => '<circle cx="18" cy="20" r="11"/><circle cx="46" cy="20" r="11"/><path d="M29 18q3-3 6 0"/>',
        'square' => '<rect x="6" y="9" width="23" height="22" rx="4"/><rect x="35" y="9" width="23" height="22" rx="4"/><path d="M29 17q3-3 6 0"/>',
        'rectangle' => '<rect x="4" y="12" width="25" height="16" rx="4"/><rect x="35" y="12" width="25" height="16" rx="4"/><path d="M29 18q3-3 6 0"/>',
        'oval' => '<ellipse cx="17" cy="20" rx="13" ry="9"/><ellipse cx="47" cy="20" rx="13" ry="9"/><path d="M30 18q2-2 4 0"/>',
        'wayfarer' => '<path d="M4 11h25l-3 15q-1 4-5 4H10q-4 0-5-4Z"/><path d="M35 11h25l-1 15q-1 4-5 4h-11q-4 0-5-4Z"/><path d="M29 15q3-3 6 0"/>',
        'cat-eye' => '<path d="M3 10q12 1 26 3-1 14-12 15T3 10Z"/><path d="M61 10q-12 1-26 3 1 14 12 15t14-18Z"/>',
        'aviator' => '<path d="M5 12q0-3 4-3h18q3 0 2 4-2 12-12 14T5 12Z"/><path d="M59 12q0-3-4-3H37q-3 0-2 4 2 12 12 14t12-15Z"/><path d="M27 9h10"/>',
        'geometric' => '<path d="M10 8h14l6 12-6 12H10L4 20Z"/><path d="M40 8h14l6 12-6 12H40l-6-12Z"/>',
    ];
    // Face-shape style guidance (not medical advice); only shapes we actually stock are linked.
    $faces = [
        ['Round face', 'Angular frames add definition.', ['rectangle', 'square', 'wayfarer', 'geometric']],
        ['Square face', 'Soft curves balance strong lines.', ['round', 'oval', 'cat-eye']],
        ['Oval face', 'Balanced proportions suit most styles.', ['square', 'rectangle', 'round', 'wayfarer']],
        ['Heart face', 'Lighter, rounder bottoms balance a wider brow.', ['round', 'oval', 'rectangle']],
    ];
@endphp

@section('content')
    {{-- 2 · CINEMATIC 3D HERO --}}
    <section class="relative isolate overflow-hidden bg-[#101113] text-white" data-gsap>
        {{-- poster fallback (visible until/unless WebGL loads) --}}
        <div class="absolute inset-0 -z-10">
            <img src="{{ asset('images/hero/store-front-md.webp') }}" alt="" class="size-full scale-110 object-cover opacity-[.07] blur-[2px]" fetchpriority="high">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_70%_45%,rgba(182,154,112,.28),transparent_60%)]"></div>
        </div>
        <div data-hero-3d class="hero-canvas absolute inset-0 -z-10 opacity-0 transition-opacity duration-1000 [&.is-ready]:opacity-100 [&>canvas]:size-full"></div>
        <div class="container-x relative flex min-h-[640px] items-center py-20 md:min-h-[720px] lg:min-h-[calc(100svh-110px)]">
            <div class="max-w-xl max-lg:mt-auto max-lg:rounded-3xl max-lg:bg-[#101113]/55 max-lg:p-6 max-lg:backdrop-blur-sm">
                <p class="eyebrow !text-[#e8d3a2]">{{ $h->eyebrow ?? 'OpticValley · Lahore' }}</p>
                <h1 class="mt-5 text-[2.8rem] font-normal uppercase leading-[0.98] tracking-[0.02em] sm:text-6xl lg:text-[5rem]">See the world differently.</h1>
                <p class="mt-6 font-display text-xl italic text-[#e8d3a2]/90">Where precision meets timeless design.</p>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-white/60">{{ $h->subtitle ?? 'Premium frames and precision prescription lenses — visit our Gulberg III studio or shop online with Cash on Delivery across Pakistan.' }}</p>
                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a href="{{ route('shop') }}" class="btn-brand px-8 py-4">Explore Collection <x-icon name="arrow-right" class="size-4"/></a>
                    <a href="#find-your-frame" class="btn border border-white/25 px-7 py-4 text-white hover:border-[#e8d3a2] hover:text-[#e8d3a2]">Discover Your Style</a>
                </div>
                <ul class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-white/60">
                    <li class="flex items-center gap-2"><x-icon name="truck" class="size-4"/> Free delivery over {{ money(setting('free_shipping_threshold', 5000)) }}</li>
                    <li class="flex items-center gap-2"><x-icon name="tag" class="size-4"/> Cash on Delivery</li>
                    <li class="flex items-center gap-2"><x-icon name="refresh" class="size-4"/> 7-day returns</li>
                </ul>
            </div>
        </div>
        <p class="absolute bottom-4 right-5 text-[10px] uppercase tracking-[0.2em] text-white/30">Representative 3D render</p>
    </section>

    {{-- 3 · SHOP BY COLLECTION --}}
    @if ($collections->isNotEmpty())
        <section class="container-x py-20 md:py-24">
            <div class="mb-10 flex items-end justify-between gap-6">
                <div><p class="eyebrow mb-3">Shop by collection</p><h2 class="text-4xl md:text-[3.2rem]" data-gsap="words">Find your next pair.</h2></div>
                <a href="{{ route('shop') }}" class="hidden text-sm font-semibold sm:inline-flex sm:items-center sm:gap-2">All {{ $stats['products'] }} frames <x-icon name="arrow-right" class="size-4"/></a>
            </div>
            <div class="grid grid-cols-2 gap-4 {{ ['', 'md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3'][$collections->count()] ?? 'md:grid-cols-4' }} md:gap-5" data-gsap="stagger">
                @foreach ($collections as $c)
                    <a href="{{ $c->url }}" class="lux-card group relative flex aspect-[4/5] flex-col justify-between overflow-hidden p-5 md:p-6">
                        <span class="pointer-events-none absolute -right-16 -top-16 size-44 rounded-full bg-[radial-gradient(circle,rgba(232,211,162,.45),transparent_70%)] opacity-0 transition duration-700 group-hover:opacity-100"></span>
                        <span class="absolute bottom-5 left-6 font-display text-[4.5rem] leading-none text-[#151515]/[.04]">0{{ $loop->iteration }}</span>
                        <div class="flex items-start justify-between">
                            <div><h3 class="text-2xl md:text-[1.7rem]">{{ $c->name }}</h3><p class="mt-1 text-xs text-muted">{{ $c->count }} {{ \Illuminate\Support\Str::plural('style', $c->count) }}</p></div>
                            <span class="grid size-10 place-items-center rounded-full border border-[#ecebe7] transition group-hover:border-[#151515] group-hover:bg-[#151515] group-hover:text-[#e8d3a2]"><x-icon name="arrow-right" class="size-4 -rotate-45 transition group-hover:rotate-0"/></span>
                        </div>
                        @if ($c->image)<img src="{{ $c->image }}" alt="{{ $c->name }} eyewear" loading="lazy" class="relative mx-auto w-[92%] drop-shadow-[0_18px_18px_rgba(0,0,0,.12)] transition duration-700 group-hover:-translate-y-2 group-hover:scale-105">@endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 4 · TRENDING / BEST SELLERS --}}
    @if ($featured->isNotEmpty())
        <section class="py-20 md:py-24" x-data="{ tab: 'best' }">
            <div class="container-x">
                <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div><p class="eyebrow mb-2">Trending now</p><h2 class="text-4xl md:text-[3rem]" data-gsap="words">Most-loved frames.</h2></div>
                    <div class="flex gap-1 rounded-full border border-[#ecebe7] bg-white p-1 text-sm font-semibold shadow-[0_12px_30px_-22px_rgba(0,0,0,.35)]" role="tablist">
                        @foreach (['best' => 'Best Sellers', 'new' => 'New Arrivals', 'featured' => 'Featured'] as $key => $label)
                            <button role="tab" :aria-selected="tab === '{{ $key }}'" @click="tab = '{{ $key }}'" class="rounded-full px-5 py-2.5 transition" :class="tab === '{{ $key }}' ? 'bg-[#151515] text-white' : 'text-muted hover:text-ink'">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                @foreach (['best' => $bestSellers, 'new' => $newArrivals, 'featured' => $featured] as $key => $list)
                    <div x-show="tab === '{{ $key }}'" @if ($key !== 'best') x-cloak @endif x-transition.opacity.duration.400ms class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4" role="tabpanel">
                        @foreach ($list->unique('id') as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
                    </div>
                @endforeach
                <div class="mt-12 text-center"><a href="{{ route('shop') }}" class="btn-primary px-8 py-4">Shop all frames <x-icon name="arrow-right" class="size-4"/></a></div>
            </div>
        </section>
    @endif

    {{-- 5 · OPTICAL LENS EXPERIENCE --}}
    <section class="relative overflow-hidden bg-[#101113] text-white">
        <div data-lens-3d class="absolute inset-0 opacity-0 transition-opacity duration-1000 [&.is-ready]:opacity-100 [&>canvas]:size-full"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#101113] via-[#101113]/70 to-transparent"></div>
        <div class="container-x pointer-events-none relative grid min-h-[620px] items-center py-20 md:min-h-[680px]">
            <div class="pointer-events-auto max-w-lg">
                <p class="eyebrow !text-[#e8d3a2]">Precision lenses</p>
                <h2 class="mt-4 text-[2.6rem] uppercase leading-[1] tracking-[0.02em] md:text-6xl" data-gsap="words">Beyond ordinary vision.</h2>
                <p class="mt-5 font-display text-xl italic text-[#e8d3a2]/90">Precision lenses. Exceptional clarity.</p>
                <ul class="mt-8 divide-y divide-white/10 border-y border-white/10 text-sm">
                    @foreach ($lensOptions as $lens)
                        <li class="flex items-center justify-between gap-4 py-3"><span><span class="font-semibold">{{ $lens->name }}</span><span class="block text-xs text-white/50">{{ $lens->description }}</span></span><span class="shrink-0 text-[#e8d3a2]">{{ $lens->price > 0 ? '+ '.money($lens->price) : 'Included' }}</span></li>
                    @endforeach
                </ul>
                <a href="{{ route('shop', ['rx' => 1]) }}" class="btn-brand mt-8 px-8 py-4">Explore lens options</a>
                <p class="mt-4 text-[11px] text-white/35">Move your cursor over the chart — an artistic visual, not a lens test.</p>
            </div>
        </div>
    </section>

    {{-- 6 · FIND YOUR PERFECT FRAME --}}
    <section id="find-your-frame" class="container-x scroll-mt-24 py-20 md:py-28" x-data="{ f: 0 }">
        <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div>
                <p class="eyebrow mb-3">Find your perfect frame</p>
                <h2 class="text-4xl leading-tight md:text-[3.2rem]" data-gsap="words">Frames that flatter your face.</h2>
                <p class="mt-5 max-w-md text-muted">Pick the face shape closest to yours to see styles that usually balance it. This is style guidance only — not medical or optical advice. When in doubt, try any frame on virtually or visit our studio.</p>
                <div class="mt-8 grid grid-cols-2 gap-3" role="tablist">
                    @foreach ($faces as $i => [$face])
                        <button type="button" role="tab" @click="f = {{ $i }}" :aria-selected="f === {{ $i }}" class="rounded-2xl border px-5 py-4 text-left text-sm font-semibold transition duration-300" :class="f === {{ $i }} ? 'border-[#151515] bg-[#151515] text-[#e8d3a2] shadow-[0_18px_36px_-20px_rgba(0,0,0,.6)]' : 'border-[#ecebe7] bg-white hover:border-[#b69a70]'">{{ $face }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                @foreach ($faces as $i => [$face, $tip, $recommended])
                    @php $available = collect($recommended)->filter(fn ($s) => isset($shapes[$s])); @endphp
                    <div x-show="f === {{ $i }}" @if ($i) x-cloak @endif x-transition.opacity class="lux-card relative p-6 md:p-10">
                        <p class="font-display text-2xl">{{ $face }}</p>
                        <p class="mt-1 text-muted">{{ $tip }}</p>
                        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @forelse ($available as $shape)
                                <a href="{{ route('shop', ['shape' => [$shape]]) }}" class="group flex flex-col items-center rounded-2xl border border-[#ecebe7] bg-white px-4 py-6 transition duration-500 hover:-translate-y-1 hover:border-[#151515] hover:bg-[#151515] hover:text-white">
                                    <svg viewBox="0 0 64 40" class="h-10 w-auto text-ink transition group-hover:text-[#e8d3a2]" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linejoin="round">{!! $shapeSvg[$shape] !!}</svg>
                                    <span class="mt-3 text-sm font-semibold">{{ ucwords(str_replace('-', ' ', $shape)) }}</span>
                                    <span class="text-xs text-muted">{{ $shapes[$shape] }} {{ \Illuminate\Support\Str::plural('style', $shapes[$shape]) }}</span>
                                </a>
                            @empty
                                <p class="col-span-full text-sm text-muted">We're adding more styles for this face shape soon.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 7 · VIRTUAL TRY-ON --}}
    @if ($tryOnFrames->isNotEmpty())
        <section class="relative overflow-hidden bg-[#101113] text-white">
            <div class="pointer-events-none absolute -left-40 top-1/2 size-[520px] -translate-y-1/2 rounded-full bg-[#b69a70]/15 blur-[120px]"></div>
            <div class="container-x relative grid gap-12 py-20 md:py-24 lg:grid-cols-[1fr_1.15fr] lg:items-center">
                <div>
                    <p class="eyebrow !text-[#e8d3a2]">Virtual Try-On</p>
                    <h2 class="mt-4 text-4xl leading-tight md:text-[3.2rem]" data-gsap="words">See it on you, before you buy.</h2>
                    <p class="mt-5 max-w-md text-white/60">Turn on your camera or upload a photo and the frame follows your face in real time. It all runs on your device — nothing is uploaded or stored.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('tryon:open'))" class="btn-brand px-8 py-4"><x-icon name="eye" class="size-4"/> Try frames on now</button>
                        <a href="{{ route('tryon') }}" class="btn border border-white/25 px-7 py-4 text-white hover:border-[#e8d3a2]">All try-on frames</a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4" data-gsap="stagger">
                    @foreach ($tryOnFrames as $p)
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('tryon:open', { detail: { id: {{ $p->id }} } }))" class="group relative overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/[.04] p-4 text-left text-white backdrop-blur transition duration-500 hover:-translate-y-1 hover:border-[#b69a70]/60 hover:bg-white/[.07]">
                            <span class="block rounded-2xl bg-white p-2"><img src="{{ $p->image_url }}" alt="{{ $p->name }}" loading="lazy" class="aspect-[4/3] w-full object-contain transition duration-700 group-hover:scale-105"></span>
                            <span class="mt-2 block truncate text-sm font-semibold">{{ $p->name }}</span>
                            <span class="mt-0.5 flex items-center justify-between text-xs text-white/55">{{ money($p->final_price) }} <span class="inline-flex items-center gap-1 font-semibold text-[#e8d3a2]"><x-icon name="eye" class="size-3.5"/> Try on</span></span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 8 · PRESCRIPTION MADE SIMPLE --}}
    <section class="container-x py-20 md:py-28">
        <div class="text-center"><p class="eyebrow mb-3">Prescription made simple</p><h2 class="mx-auto max-w-2xl text-4xl md:text-[3rem]" data-gsap="words">Your prescription, in three easy steps.</h2></div>
        <ol class="mt-14 grid gap-5 md:grid-cols-3" data-gsap="stagger">
            @foreach ([['Choose your frame', 'Browse '.$stats['products'].' frames marked "Rx ready", from '.money($stats['from']).'.', route('shop', ['rx' => 1]), 'Browse frames'], ['Select your lenses', 'Single vision, blue-light, anti-glare, photochromic or progressive — prices shown upfront.', '#', null], ['Add your prescription', 'Type SPH / CYL / AXIS values or upload a photo or PDF. Our optician confirms before fitting.', '#', null]] as $n => [$t, $d, $href, $cta])
                <li class="lux-card relative overflow-hidden p-8"><span class="absolute inset-x-0 top-0 h-1 gold-grad opacity-80"></span>
                    <span class="font-display text-6xl text-gold-grad">0{{ $n + 1 }}</span>
                    <h3 class="mt-4 font-sans text-lg font-semibold tracking-normal">{{ $t }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $d }}</p>
                    @if ($cta)<a href="{{ $href }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold">{{ $cta }} <x-icon name="arrow-right" class="size-4"/></a>@endif
                </li>
            @endforeach
        </ol>
    </section>

    {{-- 9 · BRAND STORY --}}
    <section class="container-x pb-20 md:pb-28">
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="overflow-hidden rounded-[2rem]" data-gsap="clip"><img src="{{ asset('images/hero/store-front-md.webp') }}" alt="Inside an OpticValley studio" loading="lazy" class="aspect-[4/3.4] w-full object-cover" data-gsap="parallax"></div>
            <div>
                <p class="eyebrow mb-3">Our philosophy</p>
                <h2 class="text-4xl leading-tight md:text-[3.2rem]" data-gsap="words">Luxury meets precision.</h2>
                <p class="mt-6 max-w-lg text-muted">{{ setting('footer_about') }} Every pair is matched to its lenses and checked by our team before it reaches you — whether you visit us in Gulberg III or order from anywhere in Pakistan.</p>
                <dl class="mt-8 grid max-w-md grid-cols-2 gap-6 border-t border-line pt-8">
                    <div><dt class="text-xs uppercase tracking-[0.18em] text-muted">Studio</dt><dd class="mt-1 text-sm font-semibold">{{ setting('store_address') }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-[0.18em] text-muted">Hours</dt><dd class="mt-1 text-sm font-semibold">{{ setting('store_hours') }}</dd></div>
                </dl>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode(setting('store_address')) }}" target="_blank" rel="noopener" class="btn-primary"><x-icon name="pin" class="size-4"/> Get directions</a>
                    <a href="{{ route('page.show', 'about-us') }}" class="btn-outline">Our story</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 10 · TRUST & SERVICE (verified policies only) --}}
    <section class="py-16">
        <div class="container-x grid gap-8 sm:grid-cols-2 lg:grid-cols-5" data-gsap="stagger">
            @foreach ([
                ['truck', 'Delivery', setting('shipping_note', 'Delivered in 2–5 working days.').' Free over '.money(setting('free_shipping_threshold', 5000)).'.', route('page.show', 'shipping-policy')],
                ['refresh', 'Returns', '7-day returns on unused frames.', route('page.show', 'return-refund-policy')],
                ['tag', 'Payment', 'Cash on Delivery'.(setting('payment_bank_enabled') === '1' ? ' or bank transfer' : '').'.', route('page.show', 'faqs')],
                ['shield', 'Warranty', '6-month frame warranty against manufacturing defects.', route('page.show', 'return-refund-policy')],
                ['whatsapp', 'Support', 'WhatsApp '.setting('store_phone').' · '.setting('store_hours'), route('contact')],
            ] as [$ic, $t, $d, $href])
                <a href="{{ $href }}" class="lux-card group relative block p-6">
                    <span class="grid size-12 place-items-center rounded-full bg-[#151515] text-[#e8d3a2] transition group-hover:gold-grad group-hover:text-ink"><x-icon :name="$ic" class="size-5"/></span>
                    <p class="mt-4 font-semibold">{{ $t }}</p><p class="mt-1 text-sm leading-relaxed text-muted">{{ $d }}</p>
                </a>
            @endforeach
        </div>
    </section>

    @if ($testimonials->isNotEmpty())
        <section class="container-x py-20">
            <x-section-head eyebrow="Genuine reviews" title="What customers say"/>
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($testimonials->take(3) as $t)
                    <figure class="lux-card relative p-7"><x-stars :rating="$t->rating" size="size-4"/><blockquote class="mt-4 font-display text-lg leading-snug">“{{ $t->body }}”</blockquote><figcaption class="mt-5 text-sm font-semibold">{{ $t->name }}</figcaption></figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 11 · NEWSLETTER --}}
    <section class="container-x py-20 md:py-24">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-[#101113] px-6 py-16 text-center text-white md:px-16 md:py-20">
            <div class="pointer-events-none absolute -left-24 -top-24 size-80 rounded-full bg-[#b69a70]/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 size-80 rounded-full bg-[#b69a70]/20 blur-3xl"></div>
            <img src="{{ asset('images/logo-mark-light.png') }}" alt="" class="relative mx-auto h-12 w-auto">
            <div class="relative mx-auto mt-6 max-w-xl">
                <h2 class="text-4xl md:text-5xl">Get <em class="italic text-gold-grad">10% off</em> your first pair.</h2>
                <p class="mt-4 text-white/60">New collections and members-only offers. Use code <strong class="text-[#e8d3a2]">WELCOME10</strong> at checkout (min. order applies).</p>
                <form action="{{ route('newsletter') }}" method="POST" class="mx-auto mt-8 flex max-w-md flex-col gap-2 sm:flex-row sm:rounded-full sm:bg-white sm:p-1.5">
                    @csrf
                    <label for="nl-email" class="sr-only">Email address</label>
                    <input id="nl-email" type="email" name="email" required placeholder="Enter your email" class="field !rounded-full !border-0 sm:!ring-0">
                    <button class="btn-brand shrink-0">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    @include('store.partials.tryon-modal')
@endsection

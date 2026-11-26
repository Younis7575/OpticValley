@extends('layouts.store')

@section('content')
    {{-- HERO --}}
    @if ($heroes->isNotEmpty())
        <section x-data="slider({{ $heroes->count() }})" @mouseenter="stop" @mouseleave="play" class="relative overflow-hidden">
            <div class="relative min-h-[820px] sm:min-h-[760px] lg:min-h-[680px]">
                @foreach ($heroes as $i => $hero)
                    @if ($hero->theme === 'photo')
                    <div x-show="i === {{ $i }}" x-transition:enter="transition duration-[900ms] ease-out" x-transition:enter-start="opacity-0" x-transition:leave="transition duration-500 absolute inset-0" x-transition:leave-end="opacity-0"
                         class="absolute inset-0 overflow-hidden bg-onyx text-white" @if ($i) x-cloak @endif>
                        <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="absolute inset-0 size-full scale-105 object-cover object-center animate-[heroZoom_14s_ease-out_both]" @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                        <div class="absolute inset-0 bg-gradient-to-r from-[#0e0d0c]/90 via-[#0e0d0c]/60 to-[#0e0d0c]/5"></div>
                        <div class="absolute inset-0 bg-[#0e0d0c]/45 lg:hidden"></div>
                        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#0e0d0c]/70 to-transparent"></div>
                        <div class="container-x relative flex h-full min-h-[820px] items-center pb-20 pt-10 sm:min-h-[760px] lg:min-h-[680px]">
                            <div class="max-w-xl" x-show="i === {{ $i }}" x-transition:enter="transition duration-700 delay-200" x-transition:enter-start="opacity-0 translate-y-6">
                                @if ($hero->eyebrow)<p class="eyebrow mb-5 !text-[#e8d3a2]">{{ $hero->eyebrow }}</p>@endif
                                <h1 class="text-[2.6rem] leading-[1.04] sm:text-6xl lg:text-[4.6rem]">{{ $hero->title }}</h1>
                                <div class="gold-rule mt-7"></div>
                                @if ($hero->subtitle)<p class="mt-6 max-w-md text-base leading-relaxed text-white/75">{{ $hero->subtitle }}</p>@endif
                                <div class="mt-9 flex flex-wrap items-center gap-3">
                                    @if ($hero->link)<a href="{{ $hero->link }}" class="btn-brand px-8 py-4">{{ $hero->button_text ?: 'Shop now' }} <x-icon name="arrow-right" class="size-4"/></a>@endif
                                    <a href="{{ route('shop', ['rx' => 1]) }}" class="btn border border-white/30 px-6 py-4 text-white backdrop-blur hover:border-[#e8d3a2] hover:text-[#e8d3a2]"><x-icon name="eye" class="size-4"/> Prescription ready</a>
                                </div>
                                <div class="mt-10 flex flex-wrap items-center gap-6 text-xs font-semibold text-white/70">
                                    <span class="flex items-center gap-2"><x-icon name="truck" class="size-4"/> Free delivery over {{ money(setting('free_shipping_threshold', 5000)) }}</span>
                                    <span class="flex items-center gap-2"><x-icon name="shield" class="size-4"/> Optician checked</span>
                                    <span class="flex items-center gap-2"><x-icon name="pin" class="size-4"/> Visit us in Gulberg III</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    @php $dark = $hero->theme === 'dark'; @endphp
                    <div x-show="i === {{ $i }}" x-transition:enter="transition duration-[900ms] ease-out" x-transition:enter-start="opacity-0" x-transition:leave="transition duration-500 absolute inset-0" x-transition:leave-end="opacity-0"
                         class="absolute inset-0 {{ $dark ? 'bg-onyx text-white' : 'bg-white text-ink' }}" @if ($i) x-cloak @endif>
                        {{-- decorative rings --}}
                        <div class="pointer-events-none absolute -right-40 top-1/2 size-[780px] -translate-y-1/2 rounded-full border {{ $dark ? 'border-[#b8914f]/25' : 'border-[#b8914f]/20' }}"></div>
                        <div class="pointer-events-none absolute -right-10 top-1/2 size-[520px] -translate-y-1/2 rounded-full {{ $dark ? 'bg-white/[.04]' : 'bg-sand' }}"></div>

                        <div class="container-x relative grid h-full min-h-[820px] content-center items-center gap-8 pb-20 pt-10 sm:min-h-[760px] lg:min-h-[680px] lg:grid-cols-[1.05fr_1fr] lg:py-14">
                            <div class="relative z-10 max-w-xl" x-show="i === {{ $i }}" x-transition:enter="transition duration-700 delay-200" x-transition:enter-start="opacity-0 translate-y-6">
                                @if ($hero->eyebrow)<p class="eyebrow mb-5 {{ $dark ? '!text-[#d8b47e]' : '' }}">{{ $hero->eyebrow }}</p>@endif
                                <h1 class="text-[2.6rem] leading-[1.04] sm:text-6xl lg:text-[4.6rem]">{{ $hero->title }}</h1>
                                <div class="gold-rule mt-7"></div>
                                @if ($hero->subtitle)<p class="mt-6 max-w-md text-base leading-relaxed {{ $dark ? 'text-white/70' : 'text-ink-2' }}">{{ $hero->subtitle }}</p>@endif
                                <div class="mt-9 flex flex-wrap items-center gap-3">
                                    @if ($hero->link)
                                        <a href="{{ $hero->link }}" class="{{ $dark ? 'btn-brand' : 'btn-primary' }} px-8 py-4">{{ $hero->button_text ?: 'Shop now' }} <x-icon name="arrow-right" class="size-4"/></a>
                                    @endif
                                    <a href="{{ route('shop', ['rx' => 1]) }}" class="btn px-6 py-4 {{ $dark ? 'text-white hover:bg-white/10' : 'hover:bg-white' }}"><x-icon name="eye" class="size-4"/> Prescription ready</a>
                                </div>
                                <div class="mt-8 flex items-center gap-6 text-xs font-semibold lg:mt-12 {{ $dark ? 'text-white/60' : 'text-muted' }}">
                                    <span class="flex items-center gap-2"><x-icon name="truck" class="size-4"/> Free delivery over {{ money(setting('free_shipping_threshold', 5000)) }}</span>
                                    <span class="hidden items-center gap-2 sm:flex"><x-icon name="shield" class="size-4"/> UV400 & optician checked</span>
                                </div>
                            </div>
                            <div class="relative max-lg:-mt-6">
                                <div class="relative mx-auto aspect-[4/3] w-full max-w-[640px] overflow-hidden rounded-[2.5rem] shadow-[0_40px_80px_-30px_rgba(19,23,28,.35)]">
                                    <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="size-full bg-white object-contain p-6" @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                                </div>
                                @if ($rating['count'] > 0)
                                <div class="absolute -bottom-5 left-2 hidden items-center gap-3 rounded-2xl bg-white px-4 py-3 text-ink shadow-xl sm:flex lg:-left-8">
                                    <span class="grid size-10 place-items-center rounded-full gold-grad text-ink"><x-icon name="star" class="size-5 fill-current"/></span>
                                    <div><p class="text-sm font-bold">{{ number_format($rating['avg'], 1) }} / 5 rating</p><p class="text-[11px] text-muted">from {{ $rating['count'] }} customer reviews</p></div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @if ($heroes->count() > 1)
                <div class="container-x pointer-events-none absolute inset-x-0 bottom-6 z-10 flex items-center justify-between">
                    <div class="pointer-events-auto flex gap-2">
                        @foreach ($heroes as $i => $h)
                            <button @click="go({{ $i }})" class="h-1.5 rounded-full transition-all duration-500" :class="i === {{ $i }} ? 'w-10 gold-grad' : 'w-4 bg-current opacity-25'" aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="pointer-events-auto hidden gap-2 sm:flex">
                        <button @click="stop(); prev(); play()" class="grid size-11 place-items-center rounded-full border border-current/20 bg-white/70 text-ink backdrop-blur hover:bg-white" aria-label="Previous"><x-icon name="chevron-left" class="size-4"/></button>
                        <button @click="stop(); next(); play()" class="grid size-11 place-items-center rounded-full border border-current/20 bg-white/70 text-ink backdrop-blur hover:bg-white" aria-label="Next"><x-icon name="chevron-right" class="size-4"/></button>
                    </div>
                </div>
            @endif
        </section>
    @endif


    {{-- TRUST STATS --}}
    <section class="relative z-10 bg-onyx text-white">
        <div class="h-px gold-grad opacity-70"></div>
        <div class="container-x grid grid-cols-2 divide-white/10 py-8 md:grid-cols-4 md:divide-x">
            @foreach ([
                [$stats['products'].'+', 'Frames in stock', 'box'],
                [money($stats['from']), 'Frames starting from', 'tag'],
                ['COD', 'Cash on Delivery nationwide', 'truck'],
                ['7 days', 'Easy returns & exchange', 'refresh'],
            ] as [$big, $small, $ic])
                <div class="reveal flex items-center gap-4 px-2 py-3 md:justify-center md:px-6" style="transition-delay: {{ $loop->index * 80 }}ms">
                    <span class="grid size-12 shrink-0 place-items-center rounded-full border border-[#b8914f]/40 text-[#e8d3a2]"><x-icon :name="$ic" class="size-5"/></span>
                    <div><p class="font-display text-2xl text-gold-grad md:text-[1.7rem]">{{ $big }}</p><p class="text-[11px] uppercase tracking-[0.16em] text-white/55">{{ $small }}</p></div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- GOLD MARQUEE --}}
    <div class="overflow-hidden border-b border-line bg-white py-4">
        <div class="flex w-max animate-marquee gap-12 font-display text-xl italic text-ink/80 md:text-2xl">
            @for ($r = 0; $r < 2; $r++)
                @foreach (['Prescription ready', 'Blue-light lenses', 'Flexible kids frames', 'Optician verified', 'Cash on Delivery', 'Free delivery over '.money(setting('free_shipping_threshold', 5000))] as $w)
                    <span class="flex items-center gap-12">{{ $w }} <span class="text-sm not-italic text-gold">◆</span></span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- EDITORIAL COLLECTIONS (BENTO) --}}
    @php
        $menFeat = $men->first(); $kidFeat = $kids->first();
        $rxCount = \App\Models\Product::active()->where('prescription_enabled', true)->count();
    @endphp
    <section class="container-x py-20 md:py-28">
        <div class="mb-12 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="eyebrow mb-3">The collections</p>
                <h2 class="max-w-2xl text-4xl leading-[1.05] md:text-[3.4rem]">Eyewear curated for <em class="italic text-gold-grad">every face</em> &amp; every age.</h2>
            </div>
            <a href="{{ route('shop') }}" class="btn-outline shrink-0">Explore all {{ $stats['products'] }} frames <x-icon name="arrow-right" class="size-4"/></a>
        </div>
        <div class="grid gap-5 md:grid-cols-6 md:grid-rows-2">
            {{-- Men --}}
            <a href="{{ route('category.show', 'mens-eyeglasses') }}" class="reveal group relative overflow-hidden rounded-[2rem] bg-onyx p-8 text-white md:col-span-4 md:row-span-2 md:min-h-[560px] md:p-12">
                <div class="pointer-events-none absolute -right-24 -top-24 size-[420px] rounded-full bg-[#b8914f]/20 blur-3xl transition duration-700 group-hover:bg-[#b8914f]/35"></div>
                <div class="pointer-events-none absolute -bottom-32 left-10 size-[380px] rounded-full border border-[#b8914f]/20"></div>
                <div class="relative flex h-full flex-col">
                    <p class="eyebrow !text-[#e8d3a2]">Men's eyeglasses</p>
                    <h3 class="mt-3 max-w-sm text-4xl leading-tight md:text-5xl">Sharp lines.<br>Lasting comfort.</h3>
                    <p class="mt-4 max-w-xs text-sm text-white/60">Lightweight frames with 180° flexible hinges — ready for your prescription.</p>
                    @if ($menFeat)
                        <img src="{{ $menFeat->image_url }}" alt="{{ $menFeat->name }}" loading="lazy" class="my-6 w-full max-w-xl self-center drop-shadow-[0_30px_40px_rgba(0,0,0,.55)] transition duration-700 group-hover:-translate-y-2 group-hover:scale-105 md:absolute md:bottom-6 md:right-0 md:my-0 md:w-[62%]">
                    @endif
                    <span class="mt-auto inline-flex w-max items-center gap-2 rounded-full gold-grad px-6 py-3 text-sm font-bold text-ink">Shop men <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1"/></span>
                </div>
            </a>
            {{-- Kids --}}
            <a href="{{ route('category.show', 'kids-eyeglasses') }}" class="reveal group relative overflow-hidden rounded-[2rem] bg-[#f6efe2] p-8 md:col-span-2" style="transition-delay:80ms">
                <p class="eyebrow">Kids collection</p>
                <h3 class="mt-2 text-3xl leading-tight">Flexible.<br>Tough. Fun.</h3>
                @if ($kidFeat)<img src="{{ $kidFeat->image_url }}" alt="{{ $kidFeat->name }}" loading="lazy" class="mt-2 w-full drop-shadow-[0_18px_22px_rgba(143,106,47,.35)] transition duration-700 group-hover:scale-105">@endif
                <span class="inline-flex items-center gap-2 text-sm font-bold">Shop kids · from {{ money(1500) }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1"/></span>
            </a>
            {{-- Prescription --}}
            <a href="{{ route('shop', ['rx' => 1]) }}" class="reveal group relative overflow-hidden rounded-[2rem] gold-grad p-8 text-ink md:col-span-2" style="transition-delay:160ms">
                <div class="pointer-events-none absolute -bottom-10 -right-10 size-48 rounded-full border-[18px] border-white/25"></div>
                <div class="pointer-events-none absolute -bottom-2 right-20 size-28 rounded-full border-[12px] border-white/20"></div>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-ink/70">Prescription glasses</p>
                <h3 class="mt-2 text-3xl leading-tight">Your power,<br>perfectly fitted.</h3>
                <p class="mt-3 max-w-[15rem] text-sm text-ink/70">{{ $rxCount }} frames ready for single-vision, blue-light &amp; progressive lenses.</p>
                <span class="mt-6 inline-flex items-center gap-2 rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-[#e8d3a2]">Choose frame <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1"/></span>
            </a>
        </div>
    </section>

    {{-- BESTSELLERS / NEW TABS --}}
    @if ($featured->isNotEmpty())
        <section class="relative overflow-hidden border-y border-line bg-[#faf7f1] py-20 md:py-24" x-data="{ tab: 'featured' }">
            <div class="container-x">
                <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <p class="eyebrow mb-2">Curated for you</p>
                        <h2 class="text-4xl md:text-[3rem]">This season's <em class="italic text-gold-grad">favourites</em></h2>
                    </div>
                    <div class="flex gap-1 rounded-full border border-line bg-white p-1 text-sm font-semibold shadow-sm">
                        @foreach (['featured' => 'Featured', 'best' => 'Best Sellers', 'new' => 'New Arrivals'] as $key => $label)
                            <button @click="tab = '{{ $key }}'" class="rounded-full px-5 py-2.5 transition" :class="tab === '{{ $key }}' ? 'bg-onyx text-[#e8d3a2] shadow' : 'text-muted hover:text-ink'">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                @foreach (['featured' => $featured, 'best' => $bestSellers, 'new' => $newArrivals] as $key => $list)
                    <div x-show="tab === '{{ $key }}'" @if ($key !== 'featured') x-cloak @endif x-transition.opacity.duration.400ms class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4">
                        @foreach ($list as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
                    </div>
                @endforeach
                <div class="mt-12 text-center"><a href="{{ route('shop') }}" class="btn-primary px-8 py-4">View all frames <x-icon name="arrow-right" class="size-4"/></a></div>
            </div>
        </section>
    @endif

    {{-- SHOP BY SHAPE --}}
    @if ($shapes->isNotEmpty())
        @php
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
        @endphp
        <section class="container-x py-20 md:py-24">
            <div class="text-center">
                <p class="eyebrow mb-3">Find your fit</p>
                <h2 class="text-4xl md:text-[3rem]">Shop by <em class="italic text-gold-grad">frame shape</em></h2>
            </div>
            <div class="mt-12 flex flex-wrap justify-center gap-4 md:gap-6">
                @foreach ($shapes as $shape => $count)
                    <a href="{{ route('shop', ['shape' => [$shape]]) }}" class="reveal group flex w-[150px] flex-col items-center rounded-[1.75rem] border border-line bg-white px-4 py-7 transition duration-500 hover:-translate-y-1.5 hover:border-[#b8914f]/60 hover:shadow-[0_26px_50px_-30px_rgba(143,106,47,.55)] md:w-[170px]" style="transition-delay: {{ $loop->index * 60 }}ms">
                        <span class="grid h-20 w-full place-items-center rounded-2xl bg-[#faf7f1] transition group-hover:bg-onyx">
                            <svg viewBox="0 0 64 40" class="h-10 w-auto text-ink transition group-hover:text-[#e8d3a2]" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linejoin="round">{!! $shapeSvg[$shape] ?? $shapeSvg['round'] !!}</svg>
                        </span>
                        <span class="mt-4 font-semibold">{{ ucwords(str_replace('-', ' ', $shape)) }}</span>
                        <span class="text-xs text-muted">{{ $count }} {{ \Illuminate\Support\Str::plural('style', $count) }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- SPOTLIGHT --}}
    @if ($spotlight)
        <section class="relative overflow-hidden bg-onyx text-white">
            <div class="pointer-events-none absolute left-1/2 top-1/2 size-[640px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#b8914f]/15 blur-[120px]"></div>
            <div class="container-x relative grid items-center gap-12 py-20 md:py-28 lg:grid-cols-2">
                <div class="relative order-2 lg:order-1">
                    <div class="pointer-events-none absolute inset-6 rounded-full border border-[#b8914f]/25"></div>
                    <div class="pointer-events-none absolute inset-20 rounded-full border border-[#b8914f]/15"></div>
                    <img src="{{ $spotlight->image_url }}" alt="{{ $spotlight->name }}" loading="lazy" class="reveal relative mx-auto w-full max-w-[560px] drop-shadow-[0_40px_60px_rgba(0,0,0,.6)]">
                </div>
                <div class="order-1 lg:order-2">
                    <p class="eyebrow !text-[#e8d3a2]">Frame of the week</p>
                    <h2 class="mt-3 text-4xl leading-tight md:text-5xl">{{ $spotlight->name }}</h2>
                    <div class="gold-rule mt-6"></div>
                    <p class="mt-6 max-w-md text-white/65">{{ $spotlight->short_description }}</p>
                    <ul class="mt-6 grid max-w-md grid-cols-2 gap-3 text-sm text-white/75">
                        @foreach (array_filter([
                            $spotlight->frame_shape ? ucwords(str_replace('-', ' ', $spotlight->frame_shape)).' shape' : null,
                            \App\Models\Product::FRAME_TYPES[$spotlight->frame_type] ?? null,
                            $spotlight->prescription_enabled ? 'Prescription ready' : null,
                            '6-month warranty',
                        ]) as $f)
                            <li class="flex items-center gap-2"><span class="grid size-5 place-items-center rounded-full gold-grad text-ink"><x-icon name="check" class="size-3"/></span>{{ $f }}</li>
                        @endforeach
                    </ul>
                    <div class="mt-8 flex flex-wrap items-center gap-5">
                        <span class="font-display text-4xl text-gold-grad">{{ money($spotlight->final_price) }}</span>
                        <a href="{{ route('product.show', $spotlight) }}" class="btn-brand px-8 py-4">{{ $spotlight->prescription_enabled ? 'Choose lenses' : 'Shop now' }} <x-icon name="arrow-right" class="size-4"/></a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- KIDS CAROUSEL --}}
    @if ($kids->isNotEmpty())
        <section class="py-20 md:py-24" x-data>
            <div class="container-x mb-10 flex items-end justify-between gap-6">
                <div>
                    <p class="eyebrow mb-2">Little ones</p>
                    <h2 class="text-4xl md:text-[3rem]">Kids frames they'll <em class="italic text-gold-grad">love wearing</em></h2>
                </div>
                <div class="hidden gap-2 sm:flex">
                    <button @click="$refs.kr.scrollBy({ left: -340, behavior: 'smooth' })" class="grid size-12 place-items-center rounded-full border border-line hover:border-ink" aria-label="Previous"><x-icon name="chevron-left" class="size-4"/></button>
                    <button @click="$refs.kr.scrollBy({ left: 340, behavior: 'smooth' })" class="grid size-12 place-items-center rounded-full bg-onyx text-[#e8d3a2] hover:opacity-90" aria-label="Next"><x-icon name="chevron-right" class="size-4"/></button>
                </div>
            </div>
            <div x-ref="kr" class="no-scrollbar container-x flex snap-x gap-5 overflow-x-auto pb-4">
                @foreach ($kids as $p)
                    <div class="w-[260px] shrink-0 snap-start md:w-[300px]"><x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/></div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- LENS STORY --}}
    <section class="relative overflow-hidden bg-[#faf7f1]">
        <div class="container-x grid gap-12 py-20 md:py-28 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="eyebrow mb-4">Precision lenses</p>
                <h2 class="text-4xl leading-[1.05] md:text-5xl">Your prescription, <em class="italic text-gold-grad">perfected</em> in three steps.</h2>
                <p class="mt-5 max-w-md text-muted">Pick a frame, choose your lens technology, then type in your prescription or simply upload a photo. Our opticians verify every order before it is fitted.</p>
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach (\App\Models\LensOption::active()->get() as $lens)
                        <span class="chip !border-[#b8914f]/30 !bg-white">{{ $lens->name }} @if ($lens->price > 0)<span class="text-brand">+{{ money($lens->price) }}</span>@endif</span>
                    @endforeach
                </div>
                <a href="{{ route('shop', ['rx' => 1]) }}" class="btn-primary mt-8 px-8 py-4">Shop prescription frames</a>
            </div>
            <ol class="relative grid gap-4">
                <div class="absolute bottom-10 left-[38px] top-10 w-px bg-gradient-to-b from-[#b8914f] to-transparent max-sm:hidden"></div>
                @foreach ([['Choose your frame', $stats['products'].' styles, from '.money($stats['from']).'.'], ['Select your lens', 'Clear, blue-light, anti-glare, photochromic or progressive.'], ['Add your prescription', 'Enter SPH / CYL / AXIS values or upload a photo — we handle the rest.']] as $n => [$t, $d])
                    <li class="reveal relative flex gap-5 rounded-3xl border border-line bg-white p-6 shadow-[0_20px_40px_-34px_rgba(14,13,12,.4)]" style="transition-delay: {{ $n * 100 }}ms">
                        <span class="relative z-10 grid size-14 shrink-0 place-items-center rounded-full gold-grad font-display text-2xl text-ink">{{ $n + 1 }}</span>
                        <div><p class="text-lg font-semibold">{{ $t }}</p><p class="mt-1 text-sm text-muted">{{ $d }}</p></div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- WHY OPTICVALLEY --}}
    <section class="container-x py-20 md:py-28">
        <div class="text-center">
            <p class="eyebrow mb-3">The OpticValley promise</p>
            <h2 class="mx-auto max-w-2xl text-4xl md:text-[3rem]">Luxury eyewear, <em class="italic text-gold-grad">honest</em> prices.</h2>
        </div>
        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['shield', 'Optician verified', 'Every pair is checked against your prescription before it leaves our Lahore studio.'],
                ['eye', 'Precision lenses', 'Single-vision to progressive, with blue-light and anti-glare coatings.'],
                ['truck', 'Nationwide delivery', setting('shipping_note', 'Delivered in 2–5 working days across Pakistan.')],
                ['tag', 'Cash on Delivery', 'Pay at your doorstep — no card needed, anywhere in Pakistan.'],
                ['refresh', '7-day returns', 'Not the right fit? Return unused frames within 7 days.'],
                ['whatsapp', 'Real people on WhatsApp', 'Chat with our team for frame advice and order updates.'],
            ] as [$ic, $t, $d])
                <div class="reveal group rounded-[1.75rem] border border-line bg-white p-8 transition duration-500 hover:-translate-y-1 hover:border-[#b8914f]/50 hover:shadow-[0_30px_60px_-40px_rgba(143,106,47,.6)]" style="transition-delay: {{ $loop->index * 60 }}ms">
                    <span class="grid size-14 place-items-center rounded-2xl bg-onyx text-[#e8d3a2] transition group-hover:gold-grad group-hover:text-ink"><x-icon :name="$ic" class="size-6"/></span>
                    <h3 class="mt-6 font-sans text-lg font-semibold tracking-normal">{{ $t }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- VISIT THE STORE --}}
    <section class="container-x pb-20 md:pb-28">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-onyx text-white">
            <img src="{{ asset('images/hero/store-front-md.webp') }}" alt="OpticValley store" loading="lazy" class="absolute inset-0 size-full object-cover opacity-45">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0e0d0c] via-[#0e0d0c]/85 to-[#0e0d0c]/30"></div>
            <div class="relative grid gap-10 p-8 md:p-14 lg:grid-cols-[1.2fr_1fr] lg:items-center">
                <div>
                    <p class="eyebrow !text-[#e8d3a2]">Visit our studio</p>
                    <h2 class="mt-3 text-4xl leading-tight md:text-5xl">Try before you buy,<br><em class="italic text-gold-grad">in Gulberg III</em>.</h2>
                    <p class="mt-5 max-w-md text-white/65">Free eye-wear consultation, frame fitting and adjustments by our team. Walk in any day of the week.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode(setting('store_address')) }}" target="_blank" rel="noopener" class="btn-brand px-7 py-3.5"><x-icon name="pin" class="size-4"/> Get directions</a>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp_number')) }}" target="_blank" rel="noopener" class="btn border border-white/25 px-7 py-3.5 text-white hover:border-[#e8d3a2] hover:text-[#e8d3a2]"><x-icon name="whatsapp" class="size-4"/> WhatsApp us</a>
                    </div>
                </div>
                <dl class="grid gap-4 rounded-3xl border border-white/10 bg-white/[.04] p-6 backdrop-blur md:p-8">
                    <div class="flex gap-4"><x-icon name="pin" class="mt-0.5 size-5 shrink-0 text-[#e8d3a2]"/><div><dt class="text-xs uppercase tracking-[0.18em] text-white/45">Address</dt><dd class="mt-1 text-sm">{{ setting('store_address') }}</dd></div></div>
                    <div class="flex gap-4"><x-icon name="phone" class="mt-0.5 size-5 shrink-0 text-[#e8d3a2]"/><div><dt class="text-xs uppercase tracking-[0.18em] text-white/45">Phone</dt><dd class="mt-1 text-sm">{{ setting('store_phone') }}</dd></div></div>
                    <div class="flex gap-4"><x-icon name="sparkle" class="mt-0.5 size-5 shrink-0 text-[#e8d3a2]"/><div><dt class="text-xs uppercase tracking-[0.18em] text-white/45">Hours</dt><dd class="mt-1 text-sm">{{ setting('store_hours') }}</dd></div></div>
                </dl>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS (real approved reviews only) --}}
    @if ($testimonials->isNotEmpty())
        <section class="overflow-hidden bg-[#faf7f1] py-20 md:py-24">
            <div class="container-x"><x-section-head eyebrow="Loved across Pakistan" title="What our customers <em class='italic text-gold-grad'>see</em>"/></div>
            <div class="no-scrollbar container-x flex snap-x gap-5 overflow-x-auto pb-4">
                @foreach ($testimonials as $t)
                    <figure class="card flex min-w-[300px] max-w-[360px] snap-start flex-col justify-between p-7 md:min-w-[360px]">
                        <div><x-stars :rating="$t->rating" size="size-4"/><blockquote class="mt-4 font-display text-lg leading-snug">“{{ $t->body }}”</blockquote></div>
                        <figcaption class="mt-6 border-t border-line pt-5 text-sm font-bold">{{ $t->name }} <span class="font-normal text-muted">· Verified buyer</span></figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- JOURNAL --}}
    @if ($posts->isNotEmpty())
        <section class="container-x py-20 md:py-24">
            <x-section-head eyebrow="The journal" title="Eye-care, <em class='italic text-gold-grad'>explained</em>" :link="route('blog.index')" link-text="All articles"/>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="group">
                        <div class="aspect-[16/10] overflow-hidden rounded-[1.6rem] bg-[#faf7f1] ring-1 ring-line"><img src="{{ $post->image_url }}" alt="" loading="lazy" class="size-full object-contain p-6 transition duration-700 group-hover:scale-105"></div>
                        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.18em] text-muted">{{ $post->published_at->format('M d, Y') }}</p>
                        <h3 class="mt-1.5 text-xl leading-snug"><span class="link-u">{{ $post->title }}</span></h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- NEWSLETTER --}}
    <section class="container-x pb-20 md:pb-24">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-onyx px-6 py-16 text-center text-white md:px-16 md:py-20">
            <div class="pointer-events-none absolute -left-24 -top-24 size-80 rounded-full bg-[#b8914f]/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 size-80 rounded-full bg-[#b8914f]/20 blur-3xl"></div>
            <img src="{{ asset('images/logo-mark-light.png') }}" alt="" class="relative mx-auto h-12 w-auto opacity-90">
            <div class="relative mx-auto mt-6 max-w-xl">
                <h2 class="text-4xl md:text-5xl">Get <em class="italic text-gold-grad">10% off</em> your first pair.</h2>
                <p class="mt-4 text-white/60">Join for early access to new collections and members-only offers. Use code <strong class="text-[#e8d3a2]">WELCOME10</strong> at checkout.</p>
                <form action="{{ route('newsletter') }}" method="POST" class="mx-auto mt-8 flex max-w-md flex-col gap-2 sm:flex-row sm:rounded-full sm:bg-white sm:p-1.5">
                    @csrf
                    <input type="email" name="email" required placeholder="Enter your email" class="field !rounded-full !border-0 sm:!ring-0" aria-label="Email address">
                    <button class="btn-brand shrink-0">Subscribe</button>
                </form>
            </div>
        </div>
    </section>
@endsection

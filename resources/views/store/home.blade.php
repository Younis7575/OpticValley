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

    {{-- SHOP BY CATEGORY --}}
    <section class="container-x py-20 md:py-24">
        <x-section-head eyebrow="Find your fit" title="Shop by <em class='italic text-gold-grad'>category</em>" :link="route('shop')" link-text="Browse everything"/>
        <div class="no-scrollbar -mx-4 flex snap-x gap-4 overflow-x-auto px-4 pb-2 md:mx-0 md:grid md:grid-cols-4 md:overflow-visible md:px-0 lg:grid-cols-4">
            @php $tones = array_fill(0, 8, 'bg-white ring-1 ring-line hover:ring-[#b8914f]/60 hover:shadow-[0_30px_60px_-35px_rgba(143,106,47,.5)]'); @endphp
            @foreach ($categories as $k => $c)
                @php $sample = \App\Models\Product::active()->inCategory($c)->with('primaryImage')->orderByDesc('is_featured')->first(); @endphp
                @continue(! $sample)
                <a href="{{ route('category.show', $c) }}" class="reveal group relative flex min-w-[220px] snap-start flex-col overflow-hidden rounded-[1.75rem] {{ $tones[$k % 8] }} p-6 transition duration-500 hover:-translate-y-1" style="transition-delay: {{ $k * 50 }}ms">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl leading-tight">{{ $c->name }}</h3>
                            <p class="mt-1 text-xs font-semibold text-muted">Explore collection</p>
                        </div>
                        <span class="grid size-9 place-items-center rounded-full bg-sand transition group-hover:gold-grad group-hover:text-ink"><x-icon name="arrow-right" class="size-4 -rotate-45 transition group-hover:rotate-0"/></span>
                    </div>
                    <div class="mt-4 aspect-[4/3] overflow-hidden">
                        @if ($c->image_url || $sample)
                            <img src="{{ $c->image_url ?? $sample->image_url }}" alt="{{ $c->name }}" loading="lazy" class="size-full object-contain transition duration-700 group-hover:scale-110">
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- FEATURED --}}
    @if ($featured->isNotEmpty())
        <section class="container-x pb-20 md:pb-24" x-data="{ tab: 'featured' }">
            <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <p class="eyebrow mb-2">Curated for you</p>
                    <h2 class="text-3xl md:text-[2.6rem]">This season's <em class="italic text-gold-grad">favourites</em></h2>
                </div>
                <div class="flex gap-1 rounded-full border border-line bg-white p-1 text-sm font-semibold">
                    @foreach (['featured' => 'Featured', 'best' => 'Best Sellers', 'new' => 'New Arrivals'] as $key => $label)
                        <button @click="tab = '{{ $key }}'" class="rounded-full px-4 py-2 transition" :class="tab === '{{ $key }}' ? 'bg-ink text-white' : 'text-muted hover:text-ink'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            @foreach (['featured' => $featured, 'best' => $bestSellers, 'new' => $newArrivals] as $key => $list)
                <div x-show="tab === '{{ $key }}'" @if ($key !== 'featured') x-cloak @endif x-transition.opacity.duration.400ms class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4">
                    @foreach ($list as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
                </div>
            @endforeach
        </section>
    @endif

    {{-- PROMO TILES --}}
    @if ($promos->isNotEmpty())
        <section class="container-x pb-20 md:pb-24">
            <div class="grid gap-4 md:grid-cols-3 md:gap-5">
                @foreach ($promos as $k => $promo)
                    @php $dark = $promo->theme === 'dark'; @endphp
                    <a href="{{ $promo->link ?: route('shop') }}" class="reveal group relative flex min-h-[340px] flex-col justify-between overflow-hidden rounded-[2rem] p-8 {{ $dark ? 'bg-ink text-white' : 'bg-sand' }} {{ $k === 0 ? 'md:row-span-1' : '' }}" style="transition-delay: {{ $k * 80 }}ms">
                        <img src="{{ $promo->image_url }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-[1.2s] group-hover:scale-105 {{ $dark ? 'opacity-40' : '' }}">
                        <div class="absolute inset-0 {{ $dark ? 'bg-gradient-to-t from-ink via-ink/40 to-transparent' : 'bg-gradient-to-t from-white/70 via-transparent to-transparent' }}"></div>
                        <p class="eyebrow relative {{ $dark ? '!text-[#d8b47e]' : '' }}">{{ $promo->eyebrow }}</p>
                        <div class="relative">
                            <h3 class="text-3xl">{{ $promo->title }}</h3>
                            <p class="mt-1 text-sm {{ $dark ? 'text-white/70' : 'text-ink-2' }}">{{ $promo->subtitle }}</p>
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold">{{ $promo->button_text ?: 'Shop' }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1"/></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- EYEGLASSES + SUNGLASSES split --}}
    <section class="border-y border-line/70 bg-sand py-20 md:py-24">
        <div class="container-x space-y-20">
            @foreach ([['Eyeglasses', 'eyeglasses', $eyeglasses, 'Frames that work as hard as you do', 'Lightweight acetate and titanium, ready for single-vision, blue-light or progressive lenses.'], ['Sunglasses', 'sunglasses', $sunglasses, 'Made for the Pakistani sun', 'Full UV400 protection with polarised options — aviators, cat-eyes and everything between.']] as $idx => [$label, $slug, $list, $headline, $copy])
                @if ($list->isNotEmpty())
                    <div class="grid items-center gap-10 lg:gap-14 {{ $idx ? 'lg:grid-cols-[1fr_360px]' : 'lg:grid-cols-[360px_1fr]' }}">
                        <div class="{{ $idx ? 'lg:order-2' : '' }}">
                            <p class="eyebrow mb-3">{{ $label }}</p>
                            <h2 class="text-4xl leading-[1.08]">{{ $headline }}</h2>
                            <p class="mt-4 text-sm leading-relaxed text-muted">{{ $copy }}</p>
                            <div class="mt-6 flex flex-wrap gap-2">
                                @foreach (['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids'] as $g => $gl)
                                    <a href="{{ route('category.show', $slug) }}?gender[]={{ $g }}" class="chip">{{ $gl }}</a>
                                @endforeach
                            </div>
                            <a href="{{ route('category.show', $slug) }}" class="btn-primary mt-8">Shop {{ $label }} <x-icon name="arrow-right" class="size-4"/></a>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 xl:grid-cols-4 {{ $idx ? 'lg:order-1' : '' }}">
                            @foreach ($list as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    {{-- LENS STORY --}}
    <section class="relative overflow-hidden bg-onyx text-white">
        <div class="pointer-events-none absolute -left-32 -top-32 size-[520px] rounded-full border border-white/10"></div>
        <div class="pointer-events-none absolute -bottom-40 right-10 size-[420px] rounded-full bg-white/5"></div>
        <div class="container-x relative grid gap-12 py-20 md:py-24 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="eyebrow mb-4 !text-[#d8b47e]">Precision lenses</p>
                <h2 class="text-4xl leading-[1.05] md:text-5xl">Your prescription, <em class="italic text-gold-grad">perfected</em> in three steps.</h2>
                <p class="mt-5 max-w-md text-white/70">Pick a frame, choose your lens technology, then type in your prescription or simply upload a photo. Our opticians verify every order before it is fitted.</p>
                <a href="{{ route('shop', ['rx' => 1]) }}" class="btn-brand mt-8 px-8 py-4">Shop prescription frames</a>
            </div>
            <ol class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                @foreach ([['Choose your frame', 'Over '.max(20, \App\Models\Product::active()->where('prescription_enabled', true)->count()).' prescription-ready styles.'], ['Select your lens', 'Clear, blue-light, anti-glare, photochromic or progressive.'], ['Add your prescription', 'Enter SPH/CYL/AXIS values or upload a photo — we handle the rest.']] as $n => [$t, $d])
                    <li class="reveal flex gap-5 rounded-3xl border border-[#b8914f]/25 bg-white/[.03] p-6 backdrop-blur" style="transition-delay: {{ $n * 100 }}ms">
                        <span class="font-display text-4xl italic text-gold-grad">0{{ $n + 1 }}</span>
                        <div><p class="font-semibold">{{ $t }}</p><p class="mt-1 text-sm text-white/65">{{ $d }}</p></div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- SPECIAL OFFERS --}}
    @if ($offers->isNotEmpty())
        <section class="container-x py-20 md:py-24">
            <x-section-head eyebrow="Limited time" title="Special <em class='italic text-sale'>offers</em>" :link="route('shop', ['sale' => 1])" link-text="Shop the sale"/>
            <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4">
                @foreach ($offers as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
            </div>
        </section>
    @endif

    {{-- BRANDS --}}
    @if ($brands->isNotEmpty())
        <section class="border-y border-[#b8914f]/20 bg-white py-12">
            <div class="container-x">
                <p class="mb-8 text-center text-xs font-bold uppercase tracking-[0.24em] text-muted">Our featured labels</p>
                <div class="flex flex-wrap items-center justify-center gap-x-14 gap-y-6">
                    @foreach ($brands as $b)
                        <a href="{{ route('brand.show', $b) }}" class="font-display text-2xl text-ink/45 transition hover:text-ink md:text-3xl">
                            @if ($b->logo_url)<img src="{{ $b->logo_url }}" alt="{{ $b->name }}" class="h-8 w-auto opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0">@else{{ $b->name }}@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- TESTIMONIALS --}}
    @if ($testimonials->isNotEmpty())
        <section class="overflow-hidden py-20 md:py-24">
            <div class="container-x">
                <x-section-head eyebrow="Loved across Pakistan" title="What our customers <em class='italic text-gold-grad'>see</em>"/>
            </div>
            <div class="no-scrollbar container-x flex snap-x gap-5 overflow-x-auto pb-4">
                @foreach ($testimonials as $t)
                    <figure class="card flex min-w-[300px] max-w-[360px] snap-start flex-col justify-between p-7 md:min-w-[360px]">
                        <div>
                            <x-stars :rating="$t->rating" size="size-4"/>
                            <blockquote class="mt-4 font-display text-lg leading-snug">“{{ $t->body }}”</blockquote>
                        </div>
                        <figcaption class="mt-6 flex items-center justify-between gap-3 border-t border-line pt-5">
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-full bg-sand font-display text-lg">{{ mb_substr($t->name, 0, 1) }}</span>
                                <div><p class="text-sm font-bold">{{ $t->name }}</p><p class="text-[11px] text-muted">Verified buyer</p></div>
                            </div>
                            @if ($t->product)<a href="{{ route('product.show', $t->product) }}" class="text-right text-[11px] font-semibold text-muted hover:text-ink">{{ $t->product->name }}</a>@endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- JOURNAL --}}
    @if ($posts->isNotEmpty())
        <section class="container-x pb-20 md:pb-24">
            <x-section-head eyebrow="The journal" title="Eye-care, <em class='italic text-gold-grad'>explained</em>" :link="route('blog.index')" link-text="All articles"/>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="group">
                        <div class="aspect-[16/10] overflow-hidden rounded-[1.6rem] bg-sand"><img src="{{ $post->image_url }}" alt="" loading="lazy" class="size-full object-cover transition duration-700 group-hover:scale-105"></div>
                        <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.18em] text-muted">{{ $post->published_at->format('M d, Y') }}</p>
                        <h3 class="mt-1.5 text-xl leading-snug"><span class="link-u">{{ $post->title }}</span></h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- NEWSLETTER --}}
    <section class="container-x pb-20 md:pb-24">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-onyx px-6 py-16 text-center text-white md:px-16">
            <svg viewBox="0 0 64 40" class="pointer-events-none absolute -left-10 -top-6 h-56 text-[#b8914f]/20" fill="none" stroke="currentColor" stroke-width="3"><circle cx="16" cy="22" r="11"/><circle cx="48" cy="22" r="11"/><path d="M27 20q5-5 10 0"/></svg>
            <svg viewBox="0 0 64 40" class="pointer-events-none absolute -bottom-10 -right-6 h-64 text-[#b8914f]/20" fill="none" stroke="currentColor" stroke-width="3"><circle cx="16" cy="22" r="11"/><circle cx="48" cy="22" r="11"/><path d="M27 20q5-5 10 0"/></svg>
            <div class="relative mx-auto max-w-xl">
                <p class="eyebrow mb-3">Insider list</p>
                <h2 class="text-4xl md:text-5xl">Get <em class="italic text-gold-grad">10% off</em> your first pair.</h2>
                <p class="mt-4 text-white/60">Join for early access to new collections and members-only offers. Use code <strong class="text-gold-light">WELCOME10</strong> at checkout.</p>
                <form action="{{ route('newsletter') }}" method="POST" class="mx-auto mt-8 flex max-w-md flex-col gap-2 sm:flex-row sm:rounded-full sm:bg-white sm:p-1.5 sm:shadow-sm">
                    @csrf
                    <input type="email" name="email" required placeholder="Enter your email" class="field !rounded-full !border-0 sm:!ring-0" aria-label="Email address">
                    <button class="btn-brand shrink-0">Subscribe</button>
                </form>
            </div>
        </div>
    </section>
@endsection

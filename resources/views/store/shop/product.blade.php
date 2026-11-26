@extends('layouts.store')

@section('title', $product->meta_title ?: $product->name.($product->brand ? ' by '.$product->brand->name : ''))
@section('description', $product->meta_description ?: ($product->short_description ?: strip_tags($product->description)))
@section('og_type', 'product')
@section('og_image', $product->image_url)

@push('head')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'sku' => $product->sku,
    'image' => $product->images->map->url->values(),
    'description' => strip_tags($product->short_description ?: $product->description),
    'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
    'offers' => [
        '@type' => 'Offer', 'url' => route('product.show', $product), 'priceCurrency' => setting('currency_code', 'PKR'),
        'price' => number_format($product->final_price, 2, '.', ''),
        'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ],
    'aggregateRating' => $product->rating_count ? ['@type' => 'AggregateRating', 'ratingValue' => $product->rating_avg, 'reviewCount' => $product->rating_count] : null,
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@php
    $variants = $product->variants;
    $lensJson = $lenses->map(fn ($l) => ['id' => $l->id, 'name' => $l->name, 'desc' => $l->description, 'price' => (float) $l->price, 'rx' => $l->requires_prescription])->values();
    $variantJson = $variants->map(fn ($v) => ['id' => $v->id, 'name' => $v->name, 'hex' => $v->color_hex, 'stock' => $v->stock, 'adj' => (float) $v->price_adjustment])->values();
    $firstAvailable = $variants->firstWhere('stock', '>', 0) ?? $variants->first();
@endphp

@section('content')
<div class="container-x pt-6">
    <x-breadcrumbs :items="array_filter([
        $product->category?->parent?->name => $product->category?->parent ? route('category.show', $product->category->parent) : null,
        $product->category?->name => $product->category ? route('category.show', $product->category) : null,
        $product->name => null,
    ], fn ($v, $k) => $k !== '', ARRAY_FILTER_USE_BOTH)"/>
</div>

<section class="container-x grid gap-10 py-8 lg:grid-cols-[1.15fr_1fr] lg:gap-16"
         x-data="{
            img: 0,
            qty: 1,
            variants: {{ Js::from($variantJson) }},
            lenses: {{ Js::from($lensJson) }},
            variantId: {{ $firstAvailable?->id ?? 'null' }},
            mode: 'frame',
            lensId: null,
            base: {{ $product->final_price }},
            baseStock: {{ $product->stock }},
            get variant() { return this.variants.find(v => v.id === this.variantId) },
            get lens() { return this.mode === 'rx' ? this.lenses.find(l => l.id === this.lensId) : null },
            get stock() { return this.variant ? this.variant.stock : this.baseStock },
            get unit() { return this.base + (this.variant?.adj || 0) + (this.lens?.price || 0) },
            fmt(n) { return '{{ setting('currency_symbol', 'Rs.') }} ' + Math.round(n).toLocaleString('en-US') },
         }">
    {{-- GALLERY --}}
    <div class="lg:sticky lg:top-24 lg:self-start">
        <div class="flex flex-col-reverse gap-4 md:flex-row">
            @if ($product->images->count() > 1)
                <div class="no-scrollbar flex gap-3 overflow-x-auto md:w-20 md:flex-col">
                    @foreach ($product->images as $i => $image)
                        <button type="button" @click="img = {{ $i }}" class="aspect-square w-20 shrink-0 overflow-hidden rounded-2xl border-2 bg-sand transition" :class="img === {{ $i }} ? 'border-ink' : 'border-transparent opacity-70 hover:opacity-100'" aria-label="View image {{ $i + 1 }}">
                            <img src="{{ $image->url }}" alt="" class="size-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
            <div class="relative flex-1 overflow-hidden rounded-[2rem] bg-sand" x-data="{ zoom: false, x: 50, y: 50 }"
                 @mousemove="const r = $el.getBoundingClientRect(); x = (($event.clientX - r.left) / r.width) * 100; y = (($event.clientY - r.top) / r.height) * 100" @mouseenter="zoom = true" @mouseleave="zoom = false">
                @foreach ($product->images as $i => $image)
                    <img x-show="img === {{ $i }}" @if ($i) x-cloak @endif src="{{ $image->url }}" alt="{{ $image->alt ?? $product->name }}"
                         class="aspect-[4/3.3] w-full object-cover transition-transform duration-300" :style="zoom ? `transform: scale(1.8); transform-origin: ${x}% ${y}%` : ''" @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                @endforeach
                @if ($product->images->isEmpty())<img src="{{ asset('images/placeholder.svg') }}" alt="" class="aspect-[4/3.3] w-full object-cover">@endif
                <div class="pointer-events-none absolute left-5 top-5 flex gap-2">
                    @if ($product->hasDiscount())<span class="rounded-full bg-sale px-3 py-1 text-xs font-bold text-white">-{{ $product->discount_percent }}%</span>@endif
                    @if ($product->is_new)<span class="rounded-full bg-white px-3 py-1 text-xs font-bold">New</span>@endif
                </div>
            </div>
        </div>
    </div>

    {{-- DETAILS --}}
    <div>
        @if ($product->brand)<a href="{{ route('brand.show', $product->brand) }}" class="eyebrow hover:text-ink">{{ $product->brand->name }}</a>@endif
        <h1 class="mt-2 text-4xl leading-tight md:text-[2.75rem]">{{ $product->name }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">
            @if ($product->rating_count)
                <a href="#reviews" class="flex items-center gap-2 hover:text-ink"><x-stars :rating="$product->rating_avg" size="size-4"/> {{ number_format($product->rating_avg, 1) }} ({{ $product->rating_count }} reviews)</a>
            @endif
            <span>SKU: {{ $product->sku }}</span>
        </div>

        <div class="mt-6 flex items-baseline gap-3">
            <span class="text-3xl font-bold {{ $product->hasDiscount() ? 'text-sale' : '' }}" x-text="fmt(unit)">{{ money($product->final_price) }}</span>
            @if ($product->hasDiscount())
                <span class="text-lg text-muted line-through">{{ money($product->price) }}</span>
                <span class="rounded-full bg-sale/10 px-2.5 py-1 text-xs font-bold text-sale">Save {{ money($product->price - $product->sale_price) }}</span>
            @endif
        </div>
        @if ($product->short_description)<p class="mt-4 leading-relaxed text-ink-2">{{ $product->short_description }}</p>@endif

        <form action="{{ route('cart.add', $product) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-7">
            @csrf
            {{-- Colour --}}
            @if ($variants->isNotEmpty())
                <div>
                    <p class="label">Colour: <span class="normal-case tracking-normal text-ink" x-text="variant?.name"></span></p>
                    <div class="flex flex-wrap gap-2.5">
                        <template x-for="v in variants" :key="v.id">
                            <button type="button" @click="variantId = v.id" :disabled="v.stock < 1" :title="v.name + (v.stock < 1 ? ' (sold out)' : '')"
                                    class="relative grid size-11 place-items-center rounded-full border-2 transition disabled:opacity-30" :class="variantId === v.id ? 'border-ink' : 'border-transparent hover:border-line'">
                                <span class="size-8 rounded-full border border-black/10" :style="`background:${v.hex || '#ccc'}`"></span>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="variant_id" :value="variantId">
                </div>
            @endif

            {{-- Lens selection --}}
            @if ($product->prescription_enabled && $lenses->isNotEmpty())
                <div>
                    <p class="label">Lenses</p>
                    <div class="grid grid-cols-2 gap-2 rounded-2xl bg-sand p-1.5 text-sm font-semibold">
                        <button type="button" @click="mode = 'frame'; lensId = null" class="rounded-xl py-3 transition" :class="mode === 'frame' ? 'bg-white shadow-sm' : 'text-muted'">Frame only</button>
                        <button type="button" @click="mode = 'rx'; lensId = lensId || lenses[0].id" class="rounded-xl py-3 transition" :class="mode === 'rx' ? 'bg-white shadow-sm' : 'text-muted'">With prescription lenses</button>
                    </div>

                    <div x-show="mode === 'rx'" x-collapse x-cloak>
                        <div class="mt-4 space-y-2">
                            <template x-for="l in lenses" :key="l.id">
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition" :class="lensId === l.id ? 'border-ink bg-white' : 'border-line hover:border-ink/40'">
                                    <input type="radio" name="lens_id" :value="l.id" x-model.number="lensId" class="mt-1 accent-ink" :disabled="mode !== 'rx'">
                                    <span class="flex-1"><span class="block text-sm font-semibold" x-text="l.name"></span><span class="block text-xs text-muted" x-text="l.desc"></span></span>
                                    <span class="text-sm font-bold" x-text="l.price > 0 ? '+ ' + fmt(l.price) : 'Included'"></span>
                                </label>
                            </template>
                        </div>

                        <div class="mt-5 rounded-2xl border border-line bg-white p-5" x-data="{ how: 'enter' }">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-sm font-bold">Your prescription</p>
                                <div class="flex gap-1 rounded-full bg-sand p-1 text-xs font-semibold">
                                    <button type="button" @click="how = 'enter'" class="rounded-full px-3 py-1.5" :class="how === 'enter' ? 'bg-white shadow-sm' : 'text-muted'">Enter values</button>
                                    <button type="button" @click="how = 'upload'" class="rounded-full px-3 py-1.5" :class="how === 'upload' ? 'bg-white shadow-sm' : 'text-muted'">Upload photo</button>
                                </div>
                            </div>
                            <div x-show="how === 'enter'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-[380px] text-center text-xs">
                                        <thead><tr class="text-muted"><th class="w-12"></th><th class="pb-2 font-semibold">SPH</th><th class="pb-2 font-semibold">CYL</th><th class="pb-2 font-semibold">AXIS</th><th class="pb-2 font-semibold">ADD</th></tr></thead>
                                        <tbody>
                                            @foreach (['od' => 'OD (R)', 'os' => 'OS (L)'] as $eye => $eyeLabel)
                                                <tr>
                                                    <th class="pr-2 text-left font-semibold">{{ $eyeLabel }}</th>
                                                    @foreach (['sph', 'cyl', 'axis', 'add'] as $f)
                                                        <td class="p-1"><input name="rx[{{ $eye }}_{{ $f }}]" maxlength="8" inputmode="decimal" placeholder="{{ $f === 'axis' ? '0–180' : '0.00' }}" class="field !rounded-lg !px-2 !py-2 text-center" :disabled="mode !== 'rx'"></td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <div><label class="label">PD (mm)</label><input name="rx[pd]" maxlength="8" inputmode="decimal" placeholder="e.g. 63" class="field !py-2" :disabled="mode !== 'rx'"></div>
                                    <div><label class="label">Note</label><input name="rx_note" maxlength="300" placeholder="Optional" class="field !py-2" :disabled="mode !== 'rx'"></div>
                                </div>
                            </div>
                            <div x-show="how === 'upload'" x-cloak>
                                <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-line px-4 py-8 text-center text-sm text-muted hover:border-ink/40" x-data="{ name: '' }">
                                    <x-icon name="upload" class="size-6"/>
                                    <span x-text="name || 'Upload a photo or PDF of your prescription (max 5 MB)'"></span>
                                    <input type="file" name="rx_file" accept="image/*,.pdf" class="sr-only" @change="name = $event.target.files[0]?.name" :disabled="mode !== 'rx'">
                                </label>
                            </div>
                            <p class="mt-3 text-[11px] text-muted">Not sure? Place your order and our optician will confirm your prescription on WhatsApp before fitting.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Qty + CTA --}}
            <div>
                <div class="mb-3 text-sm">
                    <template x-if="stock > 5"><span class="flex items-center gap-2 font-semibold text-brand"><span class="size-2 rounded-full bg-brand"></span> In stock, ready to ship</span></template>
                    <template x-if="stock > 0 && stock <= 5"><span class="flex items-center gap-2 font-semibold text-gold"><span class="size-2 animate-pulse rounded-full bg-gold"></span> Only <span x-text="stock"></span> left</span></template>
                    <template x-if="stock < 1"><span class="flex items-center gap-2 font-semibold text-sale"><span class="size-2 rounded-full bg-sale"></span> Out of stock</span></template>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center rounded-full border border-line bg-white">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="grid size-12 place-items-center" aria-label="Decrease"><x-icon name="minus" class="size-4"/></button>
                        <input name="qty" x-model.number="qty" type="number" min="1" :max="Math.min(stock, 20)" class="w-10 bg-transparent text-center font-semibold outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none" aria-label="Quantity">
                        <button type="button" @click="qty = Math.min(Math.min(stock, 20), qty + 1)" class="grid size-12 place-items-center" aria-label="Increase"><x-icon name="plus" class="size-4"/></button>
                    </div>
                    <button class="btn-primary flex-1 !py-4" :disabled="stock < 1"><x-icon name="bag" class="size-4"/> Add to cart · <span x-text="fmt(unit * qty)"></span></button>
                    <button type="button" x-data="wishlistToggle('{{ route('wishlist.toggle', $product) }}', {{ $inWishlist ? 'true' : 'false' }}, {{ auth()->check() ? 'true' : 'false' }})" @click="toggle"
                            class="grid size-[52px] shrink-0 place-items-center rounded-full border border-line bg-white transition hover:border-ink" aria-label="Wishlist">
                        <x-icon name="heart" ::class="on ? 'fill-sale text-sale' : ''"/>
                    </button>
                </div>
                <button name="buy_now" value="1" class="btn-brand mt-3 w-full !py-4" :disabled="stock < 1">Buy it now</button>
            </div>
        </form>

        <ul class="mt-8 grid grid-cols-3 gap-3 text-center text-[11px] font-semibold text-muted">
            <li class="rounded-2xl bg-sand/70 p-3"><x-icon name="truck" class="mx-auto mb-1.5 size-5 text-ink"/>Free delivery over {{ money(setting('free_shipping_threshold', 5000)) }}</li>
            <li class="rounded-2xl bg-sand/70 p-3"><x-icon name="refresh" class="mx-auto mb-1.5 size-5 text-ink"/>7-day easy returns</li>
            <li class="rounded-2xl bg-sand/70 p-3"><x-icon name="shield" class="mx-auto mb-1.5 size-5 text-ink"/>6-month warranty</li>
        </ul>

        {{-- Accordions --}}
        <div class="mt-8 divide-y divide-line border-y border-line">
            <div x-data="{ open: true }">
                <button @click="open = !open" class="flex w-full items-center justify-between py-5 text-left text-sm font-bold uppercase tracking-[0.14em]">Description <x-icon name="plus" class="size-4 transition" ::class="open && 'rotate-45'"/></button>
                <div x-show="open" x-collapse class="prose-ov pb-6">{!! $product->description !!}</div>
            </div>
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex w-full items-center justify-between py-5 text-left text-sm font-bold uppercase tracking-[0.14em]">Specifications <x-icon name="plus" class="size-4 transition" ::class="open && 'rotate-45'"/></button>
                <div x-show="open" x-collapse class="pb-6">
                    <dl class="grid grid-cols-2 gap-x-6 text-sm">
                        @foreach (array_filter(array_merge([
                            'Frame shape' => $product->frame_shape ? ucwords(str_replace('-', ' ', $product->frame_shape)) : null,
                            'Frame material' => $product->frame_material ? ucwords($product->frame_material) : null,
                            'Frame colour' => $product->frame_color ? ucwords($product->frame_color) : null,
                            'Frame size' => $product->frame_size,
                            'Lens' => $product->lens_info,
                            'Gender' => \App\Models\Product::GENDERS[$product->gender] ?? null,
                        ], $product->specifications ?? [])) as $k => $v)
                            <div class="flex justify-between gap-3 border-b border-line/70 py-2.5"><dt class="text-muted">{{ $k }}</dt><dd class="text-right font-semibold">{{ $v }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            </div>
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex w-full items-center justify-between py-5 text-left text-sm font-bold uppercase tracking-[0.14em]">Delivery & returns <x-icon name="plus" class="size-4 transition" ::class="open && 'rotate-45'"/></button>
                <div x-show="open" x-collapse class="prose-ov pb-6">
                    <p>{{ setting('shipping_note') }} Prescription orders need an extra 3–5 working days for lens fitting.</p>
                    <p>Returns accepted within 7 days on unused frames. <a href="{{ route('page.show', 'return-refund-policy') }}">Read our return policy</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- REVIEWS --}}
<section id="reviews" class="container-x scroll-mt-24 py-16">
    <div class="grid gap-10 lg:grid-cols-[320px_1fr]">
        <div>
            <h2 class="text-3xl">Customer reviews</h2>
            <div class="mt-5 flex items-center gap-4">
                <span class="font-display text-6xl">{{ number_format($product->rating_avg, 1) }}</span>
                <div><x-stars :rating="$product->rating_avg" size="size-5"/><p class="mt-1 text-sm text-muted">Based on {{ $product->rating_count }} {{ \Illuminate\Support\Str::plural('review', $product->rating_count) }}</p></div>
            </div>
            <div x-data="{ open: {{ $errors->hasAny(['name', 'rating', 'body']) ? 'true' : 'false' }} }" class="mt-6">
                <button @click="open = !open" class="btn-outline w-full">Write a review</button>
                <form x-show="open" x-collapse x-cloak action="{{ route('product.review', $product) }}" method="POST" class="mt-4 space-y-3" x-data="{ r: {{ old('rating', 5) }} }">
                    @csrf
                    <div class="flex gap-1">
                        @for ($s = 1; $s <= 5; $s++)
                            <button type="button" @click="r = {{ $s }}" class="text-gold" aria-label="{{ $s }} stars"><svg class="size-7" viewBox="0 0 24 24" :fill="r >= {{ $s }} ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.5"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9L12 3Z"/></svg></button>
                        @endfor
                        <input type="hidden" name="rating" :value="r">
                    </div>
                    <input name="name" value="{{ old('name', auth()->user()?->name) }}" required placeholder="Your name" class="field">
                    <input name="title" value="{{ old('title') }}" placeholder="Title (optional)" class="field">
                    <textarea name="body" required minlength="10" rows="4" placeholder="Tell others about fit, comfort and quality…" class="field">{{ old('body') }}</textarea>
                    @error('body')<p class="text-xs text-sale">{{ $message }}</p>@enderror
                    <button class="btn-primary w-full">Submit review</button>
                </form>
            </div>
        </div>
        <div class="space-y-5">
            @forelse ($product->approvedReviews as $review)
                <article class="card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 place-items-center rounded-full bg-sand font-display">{{ mb_substr($review->name, 0, 1) }}</span>
                            <div><p class="text-sm font-bold">{{ $review->name }}</p><x-stars :rating="$review->rating"/></div>
                        </div>
                        <time class="text-xs text-muted">{{ $review->created_at->diffForHumans() }}</time>
                    </div>
                    @if ($review->title)<p class="mt-4 font-semibold">{{ $review->title }}</p>@endif
                    <p class="mt-1 text-sm leading-relaxed text-ink-2">{{ $review->body }}</p>
                </article>
            @empty
                <div class="card p-10 text-center text-sm text-muted">No reviews yet — be the first to share your thoughts.</div>
            @endforelse
        </div>
    </div>
</section>

@if ($related->isNotEmpty())
    <section class="container-x pb-20">
        <x-section-head eyebrow="You may also like" title="Pairs well with your style"/>
        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4">
            @foreach ($related as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
        </div>
    </section>
@endif
@endsection

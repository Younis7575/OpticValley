@props(['product', 'wishlisted' => false])
@php
    $img2 = $product->images->get(1);
    $meta = collect([$product->frame_shape ? ucwords(str_replace('-', ' ', $product->frame_shape)) : null, \App\Models\Product::FRAME_TYPES[$product->frame_type] ?? null])->filter();
    $swatch = $product->frame_color ? (\App\Support\FrameArt::COLORS[$product->frame_color][1] ?? null) : null;
@endphp
<article class="lux-card group relative flex flex-col p-2.5">
    {{-- image stage --}}
    <div class="relative overflow-hidden rounded-[1.35rem] bg-white">
        <span class="pointer-events-none absolute inset-x-8 bottom-6 h-6 rounded-[50%] bg-black/[.06] blur-xl transition duration-700 group-hover:bg-black/[.09] group-hover:blur-2xl"></span>
        <a href="{{ route('product.show', $product) }}" class="relative block aspect-[4/3.2]" aria-label="{{ $product->name }}">
            <img src="{{ $product->image_url }}" alt="{{ $product->images->first()?->alt ?? $product->name }}" loading="lazy" decoding="async"
                 class="absolute inset-0 size-full object-contain p-4 transition duration-[900ms] ease-[cubic-bezier(.22,.8,.28,1)] group-hover:-translate-y-1.5 group-hover:scale-[1.07] {{ $img2 ? 'group-hover:opacity-0' : '' }}">
            @if ($img2)
                <img src="{{ $img2->url }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 size-full object-contain p-4 opacity-0 transition duration-700 group-hover:opacity-100">
            @endif
        </a>

        <div class="pointer-events-none absolute left-3 top-3 flex flex-wrap gap-1.5">
            @if ($product->hasDiscount())<span class="lux-pill bg-[#151515] text-[#e8d3a2]">−{{ $product->discount_percent }}%</span>@endif
            @if ($product->is_new)<span class="lux-pill border border-[#b69a70]/50 bg-white text-[#8a6a35]">New</span>@endif
            @if (! $product->in_stock)<span class="lux-pill bg-[#151515]/80 text-white">Sold out</span>@endif
        </div>

        <div class="absolute right-3 top-3 flex flex-col gap-2">
            <button type="button" x-data="wishlistToggle('{{ route('wishlist.toggle', $product) }}', {{ $wishlisted ? 'true' : 'false' }}, {{ auth()->check() ? 'true' : 'false' }})" @click="toggle"
                    class="lux-icon-btn" :aria-pressed="on" aria-label="Save {{ $product->name }} to wishlist">
                <x-icon name="heart" class="size-[17px]" ::class="on ? 'fill-sale text-sale' : ''"/>
            </button>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('quickview', { detail: '{{ route('product.quick', $product) }}' }))"
                    class="lux-icon-btn translate-x-3 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 focus:translate-x-0 focus:opacity-100 max-lg:hidden" aria-label="Quick view {{ $product->name }}">
                <x-icon name="eye" class="size-[17px]"/>
            </button>
        </div>

        @if ($product->hasTryOn())
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('tryon:open', { detail: { id: {{ $product->id }} } }))"
                    class="absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-ink shadow-[0_6px_20px_-8px_rgba(0,0,0,.35)] ring-1 ring-black/5 backdrop-blur transition hover:bg-[#151515] hover:text-[#e8d3a2]">
                <x-icon name="eye" class="size-3.5"/> Try on
            </button>
        @endif
    </div>

    {{-- details --}}
    <div class="flex flex-1 flex-col px-2.5 pb-1.5 pt-4">
        <div class="flex items-center justify-between gap-2">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-[#8a6a35]">{{ $product->brand?->name }}</p>
            @if ($product->rating_count)
                <span class="flex items-center gap-1 text-[11px] text-muted"><x-stars :rating="$product->rating_avg" size="size-3"/> {{ $product->rating_count }}</span>
            @elseif ($product->prescription_enabled)
                <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Rx ready</span>
            @endif
        </div>
        <h3 class="mt-1.5 line-clamp-2 font-display text-[1.08rem] leading-snug"><a href="{{ route('product.show', $product) }}" class="hover:text-[#8a6a35]">{{ $product->name }}</a></h3>
        @if ($meta->isNotEmpty() || $swatch)
            <p class="mb-4 mt-1.5 flex items-center gap-2 text-xs text-muted">
                @if ($swatch)<span class="size-3.5 rounded-full ring-2 ring-white shadow-[0_0_0_1px_rgba(0,0,0,.12)]" style="background: {{ $swatch }}" title="{{ ucwords($product->frame_color) }}"></span>@endif
                {{ $meta->join(' · ') }}
            </p>
        @endif

        <div class="mt-auto flex items-end justify-between gap-3 border-t border-[#efede8] pt-3.5">
            <div class="leading-tight">
                @if ($product->hasDiscount())<span class="block text-[11px] text-muted line-through">{{ money($product->price) }}</span>@endif
                <span class="text-[1.05rem] font-bold tracking-tight {{ $product->hasDiscount() ? 'text-sale' : 'text-ink' }}">{{ money($product->final_price) }}</span>
                <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.14em] {{ $product->in_stock ? 'text-[#4b7a55]' : 'text-sale' }}">{{ $product->in_stock ? 'In stock' : 'Out of stock' }}</span>
            </div>
            @if ($product->in_stock)
                @if ($product->prescription_enabled)
                    <a href="{{ route('product.show', $product) }}" class="lux-cta" aria-label="Select lenses for {{ $product->name }}"><span class="max-sm:hidden">Select lenses</span><x-icon name="arrow-right" class="size-4"/></a>
                @else
                    <button type="button" x-data="quickAdd('{{ route('cart.add', $product) }}')" @click="add" :disabled="busy" class="lux-cta" aria-label="Add {{ $product->name }} to cart"><x-icon name="bag" class="size-4"/><span class="max-sm:hidden" x-text="busy ? 'Adding…' : 'Add'">Add</span></button>
                @endif
            @endif
        </div>
    </div>
</article>

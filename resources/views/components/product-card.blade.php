@props(['product', 'wishlisted' => false])
@php $img2 = $product->images->get(1); @endphp
<article class="group relative flex flex-col">
    <div class="relative overflow-hidden rounded-[1.4rem] bg-white ring-1 ring-line transition duration-500 group-hover:ring-[#b8914f]/60 group-hover:shadow-[0_24px_50px_-28px_rgba(143,106,47,.45)]">
        <a href="{{ route('product.show', $product) }}" class="block aspect-[4/3.4]">
            <img src="{{ $product->image_url }}" alt="{{ $product->images->first()?->alt ?? $product->name }}" loading="lazy" decoding="async"
                 class="absolute inset-0 size-full object-contain p-3 transition duration-700 ease-out group-hover:scale-[1.06] {{ $img2 ? 'group-hover:opacity-0' : '' }}">
            @if ($img2)
                <img src="{{ $img2->url }}" alt="{{ $img2->alt ?? $product->name }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-contain p-3 opacity-0 transition duration-700 group-hover:opacity-100">
            @endif
        </a>
        <div class="pointer-events-none absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->hasDiscount())
                <span class="rounded-full bg-onyx px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gold-light">-{{ $product->discount_percent }}%</span>
            @endif
            @if ($product->is_new)
                <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-ink shadow-sm">New</span>
            @endif
            @if (! $product->in_stock)
                <span class="rounded-full bg-ink/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Sold out</span>
            @endif
        </div>
        <button type="button" x-data="wishlistToggle('{{ route('wishlist.toggle', $product) }}', {{ $wishlisted ? 'true' : 'false' }}, {{ auth()->check() ? 'true' : 'false' }})" @click="toggle"
                class="absolute right-3 top-3 grid size-9 place-items-center rounded-full bg-white/90 text-ink shadow-sm backdrop-blur transition hover:scale-110" :aria-pressed="on" aria-label="Add to wishlist">
            <x-icon name="heart" class="size-[18px]" ::class="on ? 'fill-sale text-sale' : ''"/>
        </button>
        @if ($product->in_stock)
            <div class="absolute inset-x-3 bottom-3 translate-y-3 opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 max-lg:hidden">
                @if ($product->prescription_enabled)
                    <a href="{{ route('product.show', $product) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-white/95 py-2.5 text-xs font-bold uppercase tracking-wider text-ink shadow backdrop-blur hover:bg-ink hover:text-white">Select lenses</a>
                @else
                    <button x-data="quickAdd('{{ route('cart.add', $product) }}')" @click="add" :disabled="busy" class="flex w-full items-center justify-center gap-2 rounded-full bg-white/95 py-2.5 text-xs font-bold uppercase tracking-wider text-ink shadow backdrop-blur hover:bg-ink hover:text-white">
                        <x-icon name="bag" class="size-4"/> <span x-text="busy ? 'Adding…' : 'Add to cart'">Add to cart</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
    <div class="mt-4 flex flex-1 flex-col px-1">
        <div class="flex items-center justify-between gap-2">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">{{ $product->brand?->name }}</p>
            @if ($product->rating_count)
                <span class="flex items-center gap-1 text-[11px] text-muted"><x-stars :rating="$product->rating_avg" size="size-3"/> {{ $product->rating_count }}</span>
            @endif
        </div>
        <h3 class="mt-1 font-sans text-[15px] font-semibold leading-snug tracking-normal"><a href="{{ route('product.show', $product) }}" class="link-u">{{ $product->name }}</a></h3>
        <div class="mt-1.5 flex items-baseline gap-2">
            <span class="text-[15px] font-bold {{ $product->hasDiscount() ? 'text-sale' : '' }}">{{ money($product->final_price) }}</span>
            @if ($product->hasDiscount())
                <span class="text-xs text-muted line-through">{{ money($product->price) }}</span>
            @endif
        </div>
    </div>
</article>

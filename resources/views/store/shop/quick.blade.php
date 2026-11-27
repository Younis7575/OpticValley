<div class="grid gap-6 p-6 md:grid-cols-2 md:p-8">
    <div class="rounded-[1.5rem] bg-white ring-1 ring-line"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-contain p-4"></div>
    <div>
        <p class="eyebrow">{{ $product->brand?->name }}</p>
        <h2 class="mt-2 text-3xl leading-tight">{{ $product->name }}</h2>
        <p class="mt-3 text-2xl font-bold {{ $product->hasDiscount() ? 'text-sale' : '' }}">{{ money($product->final_price) }} @if ($product->hasDiscount())<span class="text-base font-normal text-muted line-through">{{ money($product->price) }}</span>@endif</p>
        <p class="mt-2 text-sm font-semibold {{ $product->in_stock ? 'text-brand' : 'text-sale' }}">{{ $product->in_stock ? 'In stock' : 'Out of stock' }}</p>
        @if ($product->short_description)<p class="mt-4 text-sm leading-relaxed text-ink-2">{{ $product->short_description }}</p>@endif
        <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            @foreach (array_filter(['Shape' => $product->frame_shape ? ucwords(str_replace('-', ' ', $product->frame_shape)) : null, 'Frame' => \App\Models\Product::FRAME_TYPES[$product->frame_type] ?? null, 'Colour' => $product->frame_color ? ucwords($product->frame_color) : null, 'Size' => $product->frame_size]) as $k => $v)
                <div><dt class="text-xs text-muted">{{ $k }}</dt><dd class="font-semibold">{{ $v }}</dd></div>
            @endforeach
        </dl>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('product.show', $product) }}" class="btn-primary">{{ $product->prescription_enabled ? 'Choose lenses & buy' : 'View details' }}</a>
            @if ($product->hasTryOn())<button type="button" onclick="window.dispatchEvent(new CustomEvent('tryon:open', { detail: { id: {{ $product->id }} } }))" class="btn-outline"><x-icon name="eye" class="size-4"/> Try it on</button>@endif
        </div>
    </div>
</div>

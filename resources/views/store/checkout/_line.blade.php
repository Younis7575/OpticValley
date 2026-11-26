<div class="flex gap-4">
    <a href="{{ route('product.show', $line->product) }}" class="relative w-24 shrink-0 overflow-hidden rounded-2xl bg-white ring-1 ring-line md:w-28">
        <img src="{{ $line->product->image_url }}" alt="{{ $line->product->name }}" class="aspect-square size-full object-contain p-1">
        @isset($badge)<span class="absolute -right-1 -top-1 grid size-6 place-items-center rounded-full bg-ink text-[11px] font-bold text-white">{{ $line->qty }}</span>@endisset
    </a>
    <div class="min-w-0 flex-1 text-sm">
        <a href="{{ route('product.show', $line->product) }}" class="font-semibold hover:underline">{{ $line->product->name }}</a>
        <div class="mt-1 space-y-0.5 text-xs text-muted">
            @if ($line->variant)<p>Colour: {{ $line->variant->name }}</p>@endif
            @if ($line->lens)<p>Lens: {{ $line->lens->name }} @if ($line->lens->price > 0)(+{{ money($line->lens->price) }})@endif</p>@endif
            @if ($line->prescription || $line->prescription_file)<p class="text-brand">✓ Prescription {{ $line->prescription_file ? 'uploaded' : 'added' }}</p>@endif
            @unless ($line->stock_ok)<p class="font-semibold text-sale">Only {{ $line->available }} in stock — please reduce quantity.</p>@endunless
        </div>
        {{ $slot ?? '' }}
    </div>
    <p class="shrink-0 text-sm font-bold">{{ money($line->line_total) }}</p>
</div>

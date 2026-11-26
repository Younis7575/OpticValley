@props(['rating' => 0, 'size' => 'size-3.5'])
<span class="inline-flex items-center gap-0.5 text-gold" aria-label="{{ number_format($rating, 1) }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $size }}" viewBox="0 0 24 24" fill="{{ $rating >= $i - 0.25 ? 'currentColor' : ($rating >= $i - 0.75 ? 'url(#half)' : 'none') }}" stroke="currentColor" stroke-width="1.5"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="transparent"/></linearGradient></defs><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
    @endfor
</span>

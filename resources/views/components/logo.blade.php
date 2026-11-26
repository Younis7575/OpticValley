@props(['light' => false])
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-2.5']) }} aria-label="{{ setting('store_name', 'OpticValley') }} home">
    <svg viewBox="0 0 64 40" class="h-7 w-auto {{ $light ? 'text-white' : 'text-ink' }}" fill="none" stroke="currentColor" stroke-width="4">
        <circle cx="16" cy="22" r="11"/><circle cx="48" cy="22" r="11"/><path d="M27 20q5-5 10 0"/>
        <circle cx="54" cy="7" r="3.5" fill="#b8915a" stroke="none"/>
    </svg>
    <span class="font-display text-[1.45rem] leading-none tracking-tight {{ $light ? 'text-white' : 'text-ink' }}">Optic<span class="italic text-gold">Valley</span></span>
</a>

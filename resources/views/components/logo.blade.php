@props(['light' => false, 'full' => false])
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-2.5']) }} aria-label="{{ setting('store_name', 'OpticValley') }} home">
    @if ($full)
        <img src="{{ asset($light ? 'images/logo-light.png' : 'images/logo.png') }}" alt="{{ setting('store_name', 'OpticValley') }}" class="h-28 w-auto" width="720" height="504" loading="lazy">
    @else
        <img src="{{ asset($light ? 'images/logo-mark-light.png' : 'images/logo-mark.png') }}" alt="" class="h-8 w-auto sm:h-9" width="480" height="195">
        <span class="flex flex-col leading-none">
            <span class="font-display text-[1.15rem] tracking-[0.28em] {{ $light ? 'text-white' : 'text-ink' }}">OPTIC</span>
            <span class="mt-0.5 text-[0.55rem] font-semibold tracking-[0.5em] text-gold">VALLEY</span>
        </span>
    @endif
</a>

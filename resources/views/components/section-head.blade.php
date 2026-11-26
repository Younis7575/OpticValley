@props(['eyebrow' => null, 'title', 'link' => null, 'linkText' => 'View all'])
<div class="mb-8 flex items-end justify-between gap-6 md:mb-10">
    <div>
        @if ($eyebrow)<p class="eyebrow mb-2">{{ $eyebrow }}</p>@endif
        <h2 class="text-3xl leading-tight md:text-[2.6rem]">{!! $title !!}</h2>
    </div>
    @if ($link)
        <a href="{{ $link }}" class="group hidden shrink-0 items-center gap-2 text-sm font-semibold sm:inline-flex">{{ $linkText }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1"/></a>
    @endif
</div>

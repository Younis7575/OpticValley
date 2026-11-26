@props(['items' => []])
<nav aria-label="Breadcrumb" class="text-xs text-muted">
    <ol class="flex flex-wrap items-center gap-1.5" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('home') }}" class="hover:text-ink"><span itemprop="name">Home</span></a><meta itemprop="position" content="1"></li>
        @foreach ($items as $label => $url)
            <li class="text-line">/</li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if ($url)<a itemprop="item" href="{{ $url }}" class="hover:text-ink"><span itemprop="name">{{ $label }}</span></a>@else<span itemprop="name" class="text-ink">{{ $label }}</span>@endif
                <meta itemprop="position" content="{{ $loop->iteration + 1 }}">
            </li>
        @endforeach
    </ol>
</nav>

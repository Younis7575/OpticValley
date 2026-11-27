@extends('layouts.store')

@php
    $title = $category?->name ?? $brand?->name ?? (request('q') ? 'Search: '.request('q') : (request('sale') ? 'Sale' : 'Shop all eyewear'));
    $selected = fn ($k) => array_filter((array) request()->input($k, []), 'is_scalar');
    $activeCount = collect(['brand', 'shape', 'type', 'material', 'color', 'gender'])->sum(fn ($k) => count($selected($k))) + (request('min') || request('max') ? 1 : 0) + (request('in_stock') ? 1 : 0) + (request('rx') ? 1 : 0);
@endphp

@section('title', $category?->meta_title ?: $title)
@section('description', $category?->meta_description ?: ($category?->description ?: 'Shop '.strtolower($title).' online at OpticValley. Premium frames, precision lenses, cash on delivery across Pakistan.'))
@if (request()->hasAny(['q', 'brand', 'shape', 'type', 'material', 'color', 'gender', 'min', 'max', 'sort', 'page']))
    @section('canonical', url()->current())
@endif

@section('content')
    <section class="border-b border-line bg-sand border-b border-line/70">
        <div class="container-x py-10 md:py-14">
            <x-breadcrumbs :items="array_filter([
                $category?->parent?->name => $category?->parent ? route('category.show', $category->parent) : null,
                $title => null,
            ], fn ($v, $k) => $k !== '', ARRAY_FILTER_USE_BOTH)"/>
            <div class="mt-4 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="text-4xl md:text-[3.2rem]">{{ $title }}</h1>
                    @if ($category?->description || $brand?->description)
                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">{{ $category?->description ?? $brand?->description }}</p>
                    @endif
                </div>
                <p class="text-sm text-muted">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('style', $products->total()) }}</p>
            </div>
            @if ($category && $category->children->isNotEmpty())
                <div class="no-scrollbar -mx-4 mt-6 flex gap-2 overflow-x-auto px-4">
                    @foreach ($category->children as $child)
                        <a href="{{ route('category.show', $child) }}" class="chip shrink-0">{{ $child->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="container-x py-10" x-data="{ filters: false }">
        <div class="lg:grid lg:grid-cols-[260px_1fr] lg:gap-10">
            {{-- FILTERS --}}
            <form id="filters" method="GET" action="{{ url()->current() }}" x-ref="form"
                  :class="filters ? 'fixed inset-0 z-50 overflow-y-auto bg-paper p-5' : 'max-lg:hidden'" class="lg:sticky lg:top-24 lg:self-start">
                @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                @if (request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                <div class="mb-4 flex items-center justify-between lg:hidden">
                    <p class="font-display text-2xl">Filters</p>
                    <button type="button" @click="filters = false" class="grid size-10 place-items-center rounded-full hover:bg-sand" aria-label="Close filters"><x-icon name="x"/></button>
                </div>

                <div class="space-y-1 divide-y divide-line">
                    <div class="pb-4">
                        <label class="flex cursor-pointer items-center gap-3 py-1.5 text-sm"><input type="checkbox" name="sale" value="1" @checked(request('sale')) @change="$refs.form.requestSubmit()" class="size-4 rounded accent-[#a77c3c]"> On sale</label>
                        <label class="flex cursor-pointer items-center gap-3 py-1.5 text-sm"><input type="checkbox" name="in_stock" value="1" @checked(request('in_stock')) @change="$refs.form.requestSubmit()" class="size-4 rounded accent-[#a77c3c]"> In stock only</label>
                        <label class="flex cursor-pointer items-center gap-3 py-1.5 text-sm"><input type="checkbox" name="rx" value="1" @checked(request('rx')) @change="$refs.form.requestSubmit()" class="size-4 rounded accent-[#a77c3c]"> Prescription available</label>
                    </div>

                    @foreach ([
                        ['gender', 'Gender', $facets['genders']->mapWithKeys(fn ($g) => [$g => \App\Models\Product::GENDERS[$g] ?? ucfirst($g)])],
                        ['brand', 'Brand', $facets['brands']->pluck('name', 'id')],
                        ['shape', 'Frame shape', $facets['shapes']->mapWithKeys(fn ($v) => [$v => ucwords(str_replace('-', ' ', $v))])],
                        ['type', 'Frame type', $facets['types']->mapWithKeys(fn ($v) => [$v => \App\Models\Product::FRAME_TYPES[$v] ?? ucfirst($v)])],
                        ['material', 'Frame material', $facets['materials']->mapWithKeys(fn ($v) => [$v => strtoupper($v) === 'TR90' ? 'TR90' : ucwords($v)])],
                    ] as [$key, $label, $options])
                        @if ($options->isNotEmpty())
                            <div x-data="{ open: {{ count($selected($key)) || $loop->index < 2 ? 'true' : 'false' }} }" class="py-4">
                                <button type="button" @click="open = !open" class="flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.16em]">{{ $label }} <x-icon name="chevron-down" class="size-4 transition" ::class="open && 'rotate-180'"/></button>
                                <div x-show="open" x-collapse class="mt-3 space-y-1">
                                    @foreach ($options as $val => $text)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 text-sm text-ink-2 hover:text-ink">
                                            <input type="checkbox" name="{{ $key }}[]" value="{{ $val }}" @checked(in_array((string) $val, array_map('strval', $selected($key)), true)) @change="$refs.form.requestSubmit()" class="size-4 rounded accent-[#a77c3c]"> {{ $text }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($facets['colors']->isNotEmpty())
                        <div class="py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em]">Frame colour</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($facets['colors'] as $c)
                                    @php $hex = \App\Support\FrameArt::COLORS[$c][1] ?? '#999'; $on = in_array($c, $selected('color'), true); @endphp
                                    <label class="cursor-pointer" title="{{ ucwords($c) }}">
                                        <input type="checkbox" name="color[]" value="{{ $c }}" @checked($on) @change="$refs.form.requestSubmit()" class="peer sr-only">
                                        <span class="flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs transition peer-checked:border-ink peer-checked:bg-ink peer-checked:text-white {{ $on ? '' : 'border-line bg-white' }}">
                                            <span class="size-3.5 rounded-full border border-black/10" style="background: {{ $hex }}"></span>{{ ucwords($c) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="py-4">
                        <p class="text-xs font-bold uppercase tracking-[0.16em]">Price ({{ setting('currency_symbol', 'Rs.') }})</p>
                        <div class="mt-3 flex items-center gap-2">
                            <input type="number" name="min" min="0" value="{{ request('min') }}" placeholder="Min" class="field !py-2">
                            <span class="text-muted">–</span>
                            <input type="number" name="max" min="0" value="{{ request('max') }}" placeholder="{{ $facets['priceMax'] ?: 'Max' }}" class="field !py-2">
                        </div>
                        <button class="btn-outline mt-3 w-full !py-2">Apply price</button>
                    </div>
                </div>

                <div class="sticky bottom-0 mt-4 flex gap-2 bg-paper py-3 lg:static">
                    @if ($activeCount)<a href="{{ url()->current() }}{{ request('q') ? '?q='.urlencode(request('q')) : '' }}" class="btn-ghost flex-1 !px-3">Clear all</a>@endif
                    <button type="button" @click="filters = false" class="btn-primary flex-1 lg:hidden">Show results</button>
                </div>
            </form>

            {{-- RESULTS --}}
            <div>
                <div class="mb-6 flex items-center justify-between gap-3">
                    <button @click="filters = true" class="btn-outline !px-4 !py-2.5 lg:hidden"><x-icon name="filter" class="size-4"/> Filters @if ($activeCount)<span class="grid size-5 place-items-center rounded-full bg-ink text-[10px] text-white">{{ $activeCount }}</span>@endif</button>
                    <p class="hidden text-sm text-muted lg:block">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</p>
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        @foreach (request()->except(['sort', 'page']) as $k => $v)
                            @foreach ((array) $v as $vv)<input type="hidden" name="{{ is_array($v) ? $k.'[]' : $k }}" value="{{ $vv }}">@endforeach
                        @endforeach
                        <label for="sort" class="hidden text-xs font-semibold uppercase tracking-widest text-muted sm:block">Sort</label>
                        <select id="sort" name="sort" onchange="this.form.submit()" class="field !w-auto !rounded-full !py-2.5 !pr-10">
                            @foreach (['featured' => 'Featured', 'popular' => 'Best selling', 'newest' => 'Newest', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'rating' => 'Top rated'] as $v => $l)
                                <option value="{{ $v }}" @selected(request('sort', 'featured') === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @php
                    $chipLabels = ['gender' => fn ($v) => \App\Models\Product::GENDERS[$v] ?? $v, 'brand' => fn ($v) => $facets['brands']->firstWhere('id', $v)?->name ?? $v, 'shape' => fn ($v) => ucwords(str_replace('-', ' ', $v)), 'type' => fn ($v) => \App\Models\Product::FRAME_TYPES[$v] ?? $v, 'material' => fn ($v) => ucwords($v), 'color' => fn ($v) => ucwords($v)];
                    $chips = [];
                    foreach ($chipLabels as $k => $fmt) { foreach ($selected($k) as $v) { $q = request()->except('page'); $q[$k] = array_values(array_diff((array) ($q[$k] ?? []), [$v])); $chips[] = [$fmt($v), url()->current().'?'.http_build_query(array_filter($q, fn ($x) => $x !== [] && $x !== null && $x !== ''))]; } }
                    foreach (['sale' => 'On sale', 'in_stock' => 'In stock', 'rx' => 'Prescription ready'] as $k => $l) { if (request($k)) { $chips[] = [$l, url()->current().'?'.http_build_query(request()->except([$k, 'page']))]; } }
                    if (request('min') || request('max')) { $chips[] = [money(request('min', 0)).' – '.(request('max') ? money(request('max')) : 'any'), url()->current().'?'.http_build_query(request()->except(['min', 'max', 'page']))]; }
                @endphp
                @if ($chips)
                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        @foreach ($chips as [$label, $href])<a href="{{ $href }}" class="chip !bg-[#f7f6f2]" aria-label="Remove filter {{ $label }}">{{ $label }} <x-icon name="x" class="size-3"/></a>@endforeach
                        <a href="{{ url()->current() }}{{ request('q') ? '?q='.urlencode(request('q')) : '' }}" class="text-xs font-semibold text-muted underline underline-offset-4 hover:text-ink">Clear all</a>
                    </div>
                @endif
                @if ($products->isEmpty())
                    <div class="card grid place-items-center px-6 py-24 text-center">
                        <x-icon name="search" class="size-10 text-muted"/>
                        <h2 class="mt-4 text-2xl">No frames match these filters</h2>
                        <p class="mt-2 text-sm text-muted">Try removing a filter or searching for something else.</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-6">Browse all eyewear</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 xl:grid-cols-3">
                        @foreach ($products as $p)<x-product-card :product="$p" :wishlisted="in_array($p->id, $wishlistIds)"/>@endforeach
                    </div>
                    <div class="mt-14">{{ $products->onEachSide(1)->links() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection

@props(['placeholder' => 'Search…', 'filters' => []])
<form method="GET" class="flex flex-wrap items-center gap-2">
    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="a-input !w-64 !pl-9">
    </div>
    @foreach ($filters as $fname => [$flabel, $fopts])
        <select name="{{ $fname }}" onchange="this.form.submit()" class="a-input !w-auto">
            <option value="">{{ $flabel }}</option>
            @foreach ($fopts as $k => $v)<option value="{{ $k }}" @selected(request($fname) === (string) $k)>{{ $v }}</option>@endforeach
        </select>
    @endforeach
    {{ $slot }}
    @if (request()->hasAny(array_merge(['q'], array_keys($filters))))<a href="{{ url()->current() }}" class="text-xs font-semibold text-muted hover:text-ink">Reset</a>@endif
</form>

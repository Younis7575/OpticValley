@extends('admin.layout')
@section('title', 'Settings')
@section('content')
<div class="grid gap-6 lg:grid-cols-[220px_1fr]">
    <nav class="flex gap-1 overflow-x-auto lg:flex-col">
        @foreach ($groups as $key => [$label])<a href="{{ route('admin.settings.edit', $key) }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold {{ $group === $key ? 'bg-ink text-white' : 'text-ink-2 hover:bg-white' }}">{{ $label }}</a>@endforeach
    </nav>
    <form method="POST" action="{{ route('admin.settings.update', $group) }}" class="a-card max-w-3xl space-y-5 p-6">
        @csrf @method('PUT')
        <h2 class="text-lg font-bold">{{ $groups[$group][0] }}</h2>
        @foreach ($groups[$group][1] as $key => $f)
            @if ($f[1] === 'toggle')
                <x-admin.toggle :name="$key" :label="$f[0]" :checked="($values[$key] ?? '0') === '1'" :help="$f[3] ?? null"/>
            @else
                <x-admin.field :name="$key" :type="$f[1]" :label="$f[0]" :value="$values[$key] ?? ''" :help="$f[3] ?? null" :rows="3" step="any"/>
            @endif
        @endforeach
        <button class="a-btn-primary">Save settings</button>
    </form>
</div>
@endsection

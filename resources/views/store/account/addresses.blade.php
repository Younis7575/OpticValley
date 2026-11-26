@extends('store.account._layout')
@section('title', 'Addresses')
@section('heading', 'Addresses')
@section('account')
@php $fields = function ($a = null) { return view('store.account._address-fields', ['a' => $a])->render(); }; @endphp
    <div class="grid gap-5 md:grid-cols-2">
        @foreach ($addresses as $a)
            <div class="card p-6" x-data="{ edit: false }">
                <div x-show="!edit">
                    <div class="flex items-center justify-between"><p class="font-semibold">{{ $a->label }}</p>@if ($a->is_default)<span class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-brand">Default</span>@endif</div>
                    <p class="mt-3 text-sm leading-relaxed text-ink-2">{{ $a->name }}<br>{{ $a->line1 }}@if ($a->line2), {{ $a->line2 }}@endif<br>{{ $a->city }}{{ $a->province ? ', '.$a->province : '' }} {{ $a->postal_code }}<br>{{ $a->phone }}</p>
                    <div class="mt-4 flex gap-4 text-xs font-semibold">
                        <button @click="edit = true" class="hover:underline">Edit</button>
                        <form method="POST" action="{{ route('account.addresses.destroy', $a) }}" onsubmit="return confirm('Remove this address?')">@csrf @method('DELETE')<button class="text-sale hover:underline">Remove</button></form>
                    </div>
                </div>
                <form x-show="edit" x-cloak method="POST" action="{{ route('account.addresses.update', $a) }}" class="space-y-3">@csrf @method('PUT'){!! $fields($a) !!}<div class="flex gap-2"><button class="btn-primary !py-2.5">Save</button><button type="button" @click="edit = false" class="btn-ghost">Cancel</button></div></form>
            </div>
        @endforeach
        <div class="card border-dashed p-6" x-data="{ open: {{ $addresses->isEmpty() || $errors->any() ? 'true' : 'false' }} }">
            <button x-show="!open" @click="open = true" class="flex h-full min-h-[160px] w-full flex-col items-center justify-center gap-2 text-sm font-semibold text-muted hover:text-ink"><x-icon name="plus" class="size-6"/> Add new address</button>
            <form x-show="open" x-cloak method="POST" action="{{ route('account.addresses.store') }}" class="space-y-3">@csrf<h2 class="text-xl">New address</h2>{!! $fields() !!}<button class="btn-primary !py-2.5">Save address</button></form>
        </div>
    </div>
@endsection

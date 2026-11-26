@extends('admin.layout')
@section('title', $item->exists ? 'Edit menu link' : 'Add menu link')
@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.menus.update', $item) : route('admin.menus.store') }}" class="a-card max-w-xl space-y-4 p-6">
    @csrf @if ($item->exists) @method('PUT') @endif
    <x-admin.field name="location" type="select" label="Menu" :value="$item->location" :options="\App\Models\MenuItem::LOCATIONS"/>
    <x-admin.field name="label" label="Label" :value="$item->label" required/>
    <x-admin.field name="url" label="URL" :value="$item->url" required placeholder="/category/sunglasses" help="Start with / for pages on this site, or https:// for external links."/>
    <x-admin.field name="sort_order" type="number" min="0" label="Order" :value="$item->sort_order ?? 0"/>
    <x-admin.toggle name="is_active" label="Visible" :checked="$item->is_active"/>
    <button class="a-btn-primary">Save</button>
</form>
@endsection

@extends('admin.layout')
@section('title', $lens->exists ? 'Edit lens option' : 'Add lens option')
@section('content')
<form method="POST" action="{{ $lens->exists ? route('admin.lens-options.update', $lens) : route('admin.lens-options.store') }}" class="a-card max-w-xl space-y-4 p-6">
    @csrf @if ($lens->exists) @method('PUT') @endif
    <x-admin.field name="name" label="Name" :value="$lens->name" required/>
    <x-admin.field name="description" label="Short description" :value="$lens->description"/>
    <div class="grid grid-cols-2 gap-4"><x-admin.field name="price" type="number" step="0.01" min="0" label="Extra price" :value="$lens->price ?? 0" required/><x-admin.field name="sort_order" type="number" min="0" label="Sort order" :value="$lens->sort_order ?? 0"/></div>
    <x-admin.toggle name="requires_prescription" label="Requires a prescription" :checked="$lens->requires_prescription" help="Customer must enter or upload a prescription for this lens."/>
    <x-admin.toggle name="is_active" label="Active" :checked="$lens->is_active"/>
    <button class="a-btn-primary">Save</button>
</form>
@endsection

@extends('admin.layout')
@section('title', $brand->exists ? 'Edit brand' : 'Add brand')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" class="grid max-w-4xl gap-6 lg:grid-cols-[1fr_280px]">
    @csrf @if ($brand->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $brand->slug)), {{ $brand->exists ? 'true' : 'false' }})">
        <x-admin.field name="name" label="Name" :value="$brand->name" required x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL slug</label><input name="slug" x-model="slug" @input="locked = true" class="a-input"></div>
        <x-admin.field name="description" type="textarea" rows="3" label="Description" :value="$brand->description"/>
        <x-admin.toggle name="is_active" label="Active" :checked="$brand->is_active"/>
        <x-admin.toggle name="is_featured" label="Featured on homepage" :checked="$brand->is_featured"/>
        <button class="a-btn-primary">Save</button>
    </div>
    <div class="a-card p-5"><x-admin.image name="logo" label="Logo" :current="$brand->logo_url"/></div>
</form>
@endsection

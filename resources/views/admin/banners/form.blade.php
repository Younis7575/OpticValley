@extends('admin.layout')
@section('title', $banner->exists ? 'Edit banner' : 'Add banner')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_320px]">
    @csrf @if ($banner->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6">
        <div class="grid gap-4 sm:grid-cols-2"><x-admin.field name="position" type="select" label="Position" :value="$banner->position" :options="\App\Models\Banner::POSITIONS"/><x-admin.field name="theme" type="select" label="Colour theme" :value="$banner->theme" :options="['light' => 'Light', 'dark' => 'Dark']"/></div>
        <x-admin.field name="eyebrow" label="Small heading" :value="$banner->eyebrow" placeholder="e.g. New season"/>
        <x-admin.field name="title" label="Title" :value="$banner->title" required/>
        <x-admin.field name="subtitle" type="textarea" rows="2" label="Subtitle" :value="$banner->subtitle"/>
        <div class="grid gap-4 sm:grid-cols-3"><x-admin.field name="button_text" label="Button text" :value="$banner->button_text"/><x-admin.field name="link" label="Link" :value="$banner->link" placeholder="/category/sunglasses" class="sm:col-span-2"/></div>
        <x-admin.field name="sort_order" type="number" min="0" label="Sort order" :value="$banner->sort_order ?? 0"/>
    </div>
    <div class="space-y-6">
        <div class="a-card p-5"><x-admin.image name="image" label="Banner image" :current="$banner->image_url" help="Recommended 1600×1200 (hero) or 1200×900 (promo). Max 4 MB."/></div>
        <div class="a-card space-y-4 p-5"><x-admin.toggle name="is_active" label="Active" :checked="$banner->is_active"/><button class="a-btn-primary w-full">Save banner</button></div>
    </div>
</form>
@endsection

@extends('admin.layout')
@section('title', $category->exists ? 'Edit category' : 'Add category')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_300px]">
    @csrf @if ($category->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $category->slug)), {{ $category->exists ? 'true' : 'false' }})">
        <x-admin.field name="name" label="Name" :value="$category->name" required x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL slug</label><input name="slug" x-model="slug" @input="locked = true" class="a-input"><p class="a-help">{{ url('/category') }}/<span x-text="slug"></span></p></div>
        <x-admin.field name="parent_id" type="select" label="Parent category" :value="$category->parent_id" :options="$parents"/>
        <x-admin.field name="description" type="textarea" rows="3" label="Description" :value="$category->description" help="Shown at the top of the category page."/>
        <div class="grid gap-4 sm:grid-cols-2"><x-admin.field name="meta_title" label="Meta title" :value="$category->meta_title"/><x-admin.field name="sort_order" type="number" min="0" label="Sort order" :value="$category->sort_order ?? 0"/></div>
        <x-admin.field name="meta_description" type="textarea" rows="2" label="Meta description" :value="$category->meta_description"/>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-4 p-5">
            <x-admin.toggle name="is_active" label="Active" :checked="$category->is_active"/>
            <x-admin.toggle name="show_in_menu" label="Show in main menu" :checked="$category->show_in_menu"/>
            <button class="a-btn-primary w-full">Save</button>
        </div>
        <div class="a-card p-5"><x-admin.image name="image" label="Category image" :current="$category->image_url"/></div>
    </div>
</form>
@endsection

@extends('admin.layout')
@section('title', $category->exists ? 'Edit category: '.$category->name : 'Add main category')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_300px]">
    @csrf @if ($category->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $category->slug)), {{ $category->exists ? 'true' : 'false' }})">
        <div class="rounded-xl bg-brand-50 px-4 py-3 text-xs text-ink-2">This is a <strong>main category</strong> (shown in the store menu). To create a group inside it, use <a href="{{ route('admin.subcategories.create', $category->exists ? ['parent' => $category->id] : []) }}" class="font-semibold underline">Add subcategory</a>.</div>
        <x-admin.field name="name" label="Category name" :value="$category->name" required placeholder="e.g. Eyeglasses" x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL</label><div class="flex items-center rounded-xl border border-line bg-paper pl-3 text-sm text-muted">{{ url('/category') }}/<input name="slug" x-model="slug" @input="locked = true" class="a-input !rounded-l-none !border-0 !bg-white"></div></div>
        <x-admin.field name="description" type="textarea" rows="3" label="Description" :value="$category->description" help="Shown at the top of the category page."/>
        <div class="grid gap-4 sm:grid-cols-2"><x-admin.field name="meta_title" label="SEO title" :value="$category->meta_title"/><x-admin.field name="sort_order" type="number" min="0" label="Menu order" :value="$category->sort_order ?? 0" help="Lower numbers appear first."/></div>
        <x-admin.field name="meta_description" type="textarea" rows="2" label="SEO description" :value="$category->meta_description"/>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-4 p-5">
            <x-admin.toggle name="is_active" label="Active" :checked="$category->is_active" help="Hidden categories don't appear in the store."/>
            <x-admin.toggle name="show_in_menu" label="Show in main menu" :checked="$category->show_in_menu"/>
            <button class="a-btn-primary w-full">{{ $category->exists ? 'Save category' : 'Create category' }}</button>
            @if ($category->exists)<a href="{{ route('admin.subcategories.index', ['parent' => $category->id]) }}" class="a-btn-outline w-full">Manage its subcategories ({{ $category->children_count }})</a>@endif
        </div>
        <div class="a-card p-5"><x-admin.image name="image" label="Category image" :current="$category->image_url"/></div>
    </div>
</form>
@endsection

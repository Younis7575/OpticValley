@extends('admin.layout')
@section('title', $page->exists ? 'Edit page' : 'Add page')
@section('content')
<form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="grid gap-6 xl:grid-cols-[1fr_300px]">
    @csrf @if ($page->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $page->slug)), {{ $page->exists ? 'true' : 'false' }})">
        <x-admin.field name="title" label="Title" :value="$page->title" required x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL slug</label><input name="slug" x-model="slug" @input="locked = true" class="a-input"><p class="a-help">{{ url('/pages') }}/<span x-text="slug"></span></p></div>
        <x-admin.richtext name="content" label="Content" :value="$page->content" rows="18"/>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-4 p-5"><x-admin.toggle name="is_active" label="Published" :checked="$page->is_active"/><button class="a-btn-primary w-full">Save page</button></div>
        <div class="a-card space-y-4 p-5"><p class="font-bold">SEO</p><x-admin.field name="meta_title" label="Meta title" :value="$page->meta_title"/><x-admin.field name="meta_description" type="textarea" rows="3" label="Meta description" :value="$page->meta_description"/></div>
    </div>
</form>
@endsection

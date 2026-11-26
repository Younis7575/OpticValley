@extends('admin.layout')
@section('title', $post->exists ? 'Edit article' : 'New article')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="grid gap-6 xl:grid-cols-[1fr_320px]">
    @csrf @if ($post->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $post->slug)), {{ $post->exists ? 'true' : 'false' }})">
        <x-admin.field name="title" label="Title" :value="$post->title" required x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL slug</label><input name="slug" x-model="slug" @input="locked = true" class="a-input"></div>
        <x-admin.field name="excerpt" type="textarea" rows="2" label="Excerpt" :value="$post->excerpt"/>
        <x-admin.richtext name="body" label="Article" :value="$post->body" rows="20" required/>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-4 p-5">
            <x-admin.toggle name="publish" label="Published" :checked="$post->exists ? $post->published_at !== null : true"/>
            <x-admin.field name="published_at" type="datetime-local" label="Publish date" :value="$post->published_at?->format('Y-m-d\TH:i')" help="Set a future date to schedule."/>
            <button class="a-btn-primary w-full">Save article</button>
        </div>
        <div class="a-card p-5"><x-admin.image name="image" label="Cover image" :current="$post->image_url"/></div>
        <div class="a-card space-y-4 p-5"><p class="font-bold">SEO</p><x-admin.field name="meta_title" label="Meta title" :value="$post->meta_title"/><x-admin.field name="meta_description" type="textarea" rows="3" label="Meta description" :value="$post->meta_description"/></div>
    </div>
</form>
@endsection

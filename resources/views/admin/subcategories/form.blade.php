@extends('admin.layout')
@section('title', $sub->exists ? 'Edit subcategory: '.$sub->name : 'Add subcategory')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $sub->exists ? route('admin.subcategories.update', $sub) : route('admin.subcategories.store') }}" class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_300px]">
    @csrf @if ($sub->exists) @method('PUT') @endif
    <div class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $sub->slug)), {{ $sub->exists ? 'true' : 'false' }})">
        <x-admin.field name="parent_id" type="select" label="Main category" :value="$sub->parent_id" :options="collect(['' => '— Choose a main category —'])->union($parents)" required help="The subcategory will appear inside this category's menu and page."/>
        <x-admin.field name="name" label="Subcategory name" :value="$sub->name" required placeholder="e.g. Men's Eyeglasses" x-on:input="fromName($event.target.value)"/>
        <div><label class="a-label">URL</label><div class="flex items-center rounded-xl border border-line bg-paper pl-3 text-sm text-muted">{{ url('/category') }}/<input name="slug" x-model="slug" @input="locked = true" class="a-input !rounded-l-none !border-0 !bg-white"></div></div>
        <x-admin.field name="description" type="textarea" rows="3" label="Description" :value="$sub->description"/>
        <div class="grid gap-4 sm:grid-cols-2"><x-admin.field name="meta_title" label="SEO title" :value="$sub->meta_title"/><x-admin.field name="sort_order" type="number" min="0" label="Order" :value="$sub->sort_order ?? 0" help="Lower numbers appear first."/></div>
        <x-admin.field name="meta_description" type="textarea" rows="2" label="SEO description" :value="$sub->meta_description"/>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-4 p-5">
            <x-admin.toggle name="is_active" label="Active" :checked="$sub->is_active"/>
            <input type="hidden" name="show_in_menu" value="1">
            <button class="a-btn-primary w-full">{{ $sub->exists ? 'Save subcategory' : 'Create subcategory' }}</button>
            @unless ($sub->exists)<button name="add_another" value="1" class="a-btn-outline w-full">Create &amp; add another</button>@endunless
        </div>
        <div class="a-card p-5"><x-admin.image name="image" label="Image (optional)" :current="$sub->image_url"/></div>
    </div>
</form>
@endsection

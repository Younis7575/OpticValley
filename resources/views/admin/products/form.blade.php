@extends('admin.layout')
@section('title', $product->exists ? 'Edit: '.$product->name : 'Add product')
@section('actions')
    @if ($product->exists)<a href="{{ route('product.show', $product) }}" target="_blank" class="a-btn-outline">View in store</a>@endif
@endsection
@section('content')
@php
    $specs = collect(old('spec_keys') ? array_combine(old('spec_keys'), old('spec_values')) : ($product->specifications ?? []))->map(fn ($v, $k) => ['k' => $k, 'v' => $v])->values();
    $variants = old('variants', $product->variants->map->only(['id', 'name', 'color_hex', 'sku', 'price_adjustment', 'stock'])->all());
@endphp
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="grid gap-6 xl:grid-cols-[1fr_340px]">
    @csrf @if ($product->exists) @method('PUT') @endif
    <div class="space-y-6">
        <section class="a-card space-y-4 p-6" x-data="slugger(@js(old('slug', $product->slug)), {{ $product->exists ? 'true' : 'false' }})">
            <h2 class="font-bold">General</h2>
            <x-admin.field name="name" label="Product name" :value="$product->name" required x-on:input="fromName($event.target.value)"/>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="a-label" for="f_slug">URL slug</label><input id="f_slug" name="slug" x-model="slug" @input="locked = true" class="a-input"><p class="a-help">{{ url('/product') }}/<span x-text="slug"></span></p></div>
                <x-admin.field name="sku" label="SKU" :value="$product->sku" required/>
            </div>
            <x-admin.field name="short_description" type="textarea" rows="2" label="Short description" :value="$product->short_description" help="Shown near the price and in search results (max 500 characters)."/>
            <x-admin.richtext name="description" label="Full description" :value="$product->description"/>
        </section>

        <section class="a-card p-6">
            <h2 class="font-bold">Images</h2>
            @if ($product->images->isNotEmpty())
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($product->images as $img)
                        <div class="rounded-xl border border-line p-2">
                            <img src="{{ $img->url }}" alt="" class="aspect-square w-full rounded-lg bg-paper object-cover">
                            <div class="mt-2 flex items-center gap-2">
                                <input type="number" name="image_order[{{ $img->id }}]" value="{{ $img->sort_order }}" class="a-input !w-16 !px-2 !py-1 text-xs" title="Order (lowest first = main image)">
                                <button type="submit" form="del-img-{{ $img->id }}" class="ml-auto text-xs font-semibold text-sale" onclick="return confirm('Remove this image?')">Remove</button>
                            </div>
                            <input name="image_alt[{{ $img->id }}]" value="{{ $img->alt }}" placeholder="Alt text" class="a-input mt-2 !px-2 !py-1 text-xs">
                        </div>
                    @endforeach
                </div>
            @endif
            <label class="mt-4 flex cursor-pointer flex-col items-center gap-1 rounded-xl border-2 border-dashed border-line px-4 py-8 text-center text-sm text-muted hover:border-brand" x-data="{ n: 0 }">
                <span class="font-semibold text-ink">Upload images</span>
                <span x-text="n ? n + ' file(s) selected' : 'JPG, PNG or WebP · up to 12 · 4 MB each. The lowest order number is the main image.'"></span>
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="n = $event.target.files.length">
            </label>
        </section>

        <section class="a-card space-y-4 p-6">
            <h2 class="font-bold">Pricing & inventory</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-admin.field name="price" type="number" step="0.01" min="0" label="Regular price" :value="$product->price" required/>
                <x-admin.field name="sale_price" type="number" step="0.01" min="0" label="Sale price" :value="$product->sale_price" help="Leave empty for no discount."/>
                <x-admin.field name="stock" type="number" min="0" label="Stock" :value="$product->stock ?? 0" required help="Auto-calculated when variants exist."/>
                <x-admin.field name="low_stock_threshold" type="number" min="0" label="Low-stock alert at" :value="$product->low_stock_threshold" required/>
            </div>
        </section>

        <section class="a-card p-6" x-data="repeater({{ Js::from(collect($variants)->map(fn ($v) => $v + ['_k' => uniqid()])->values()) }})">
            <div class="flex items-center justify-between"><div><h2 class="font-bold">Colour variants</h2><p class="text-xs text-muted">Each colour can have its own stock, SKU and price adjustment.</p></div><button type="button" @click="add({ color_hex: '#1a1a1a', stock: 0, price_adjustment: 0 })" class="a-btn-outline !py-2 text-xs">+ Add colour</button></div>
            <div class="mt-4 space-y-2">
                <template x-for="(row, i) in rows" :key="row._k">
                    <div class="grid grid-cols-[44px_1fr_1fr_100px_90px_auto] items-center gap-2">
                        <input type="hidden" :name="`variants[${i}][id]`" :value="row.id">
                        <input type="color" :name="`variants[${i}][color_hex]`" x-model="row.color_hex" class="h-10 w-11 cursor-pointer rounded-lg border border-line">
                        <input :name="`variants[${i}][name]`" x-model="row.name" placeholder="Colour name" class="a-input">
                        <input :name="`variants[${i}][sku]`" x-model="row.sku" placeholder="SKU (optional)" class="a-input">
                        <input type="number" step="0.01" :name="`variants[${i}][price_adjustment]`" x-model="row.price_adjustment" placeholder="+/- price" class="a-input" title="Price adjustment">
                        <input type="number" min="0" :name="`variants[${i}][stock]`" x-model="row.stock" placeholder="Stock" class="a-input" title="Stock">
                        <button type="button" @click="remove(i)" class="px-2 text-lg text-muted hover:text-sale" aria-label="Remove">×</button>
                    </div>
                </template>
                <p x-show="!rows.length" class="rounded-xl bg-paper px-4 py-6 text-center text-xs text-muted">No variants — the product uses its main stock.</p>
            </div>
        </section>

        <section class="a-card space-y-4 p-6">
            <h2 class="font-bold">Frame attributes</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <x-admin.field name="gender" type="select" label="Gender" :value="$product->gender" :options="\App\Models\Product::GENDERS"/>
                <div><label class="a-label">Frame shape</label><input name="frame_shape" list="shapes" value="{{ old('frame_shape', $product->frame_shape) }}" class="a-input"><datalist id="shapes">@foreach (\App\Models\Product::SHAPES as $s)<option value="{{ $s }}">@endforeach</datalist></div>
                <div><label class="a-label">Frame material</label><input name="frame_material" list="materials" value="{{ old('frame_material', $product->frame_material) }}" class="a-input"><datalist id="materials">@foreach (\App\Models\Product::MATERIALS as $s)<option value="{{ $s }}">@endforeach</datalist></div>
                <div><label class="a-label">Frame colour</label><input name="frame_color" list="colors" value="{{ old('frame_color', $product->frame_color) }}" class="a-input"><datalist id="colors">@foreach (array_keys(\App\Support\FrameArt::COLORS) as $s)<option value="{{ $s }}">@endforeach</datalist></div>
                <x-admin.field name="frame_size" label="Frame size" :value="$product->frame_size" placeholder="e.g. Medium (52-18-140)"/>
                <x-admin.field name="lens_info" label="Lens information" :value="$product->lens_info" placeholder="e.g. UV400 polarised"/>
            </div>
            <div x-data="repeater({{ Js::from($specs->map(fn ($s) => $s + ['_k' => uniqid()])) }})">
                <div class="flex items-center justify-between"><p class="a-label !mb-0">Extra specifications</p><button type="button" @click="add({ k: '', v: '' })" class="text-xs font-semibold text-brand">+ Add row</button></div>
                <div class="mt-2 space-y-2">
                    <template x-for="(row, i) in rows" :key="row._k">
                        <div class="flex gap-2"><input name="spec_keys[]" x-model="row.k" placeholder="e.g. Bridge" class="a-input"><input name="spec_values[]" x-model="row.v" placeholder="e.g. 18 mm" class="a-input"><button type="button" @click="remove(i)" class="px-2 text-lg text-muted hover:text-sale">×</button></div>
                    </template>
                </div>
            </div>
        </section>

        <section class="a-card space-y-4 p-6">
            <h2 class="font-bold">Search engine listing</h2>
            <x-admin.field name="meta_title" label="Meta title" :value="$product->meta_title" help="Defaults to the product name."/>
            <x-admin.field name="meta_description" type="textarea" rows="2" label="Meta description" :value="$product->meta_description" help="Defaults to the short description."/>
        </section>
    </div>

    <aside class="space-y-6 xl:sticky xl:top-24 xl:self-start">
        <section class="a-card space-y-4 p-5">
            <x-admin.toggle name="is_active" label="Visible in store" :checked="$product->is_active"/>
            <x-admin.toggle name="prescription_enabled" label="Prescription lenses available" :checked="$product->prescription_enabled" help="Shows the lens & prescription selector."/>
            <x-admin.toggle name="is_featured" label="Featured" :checked="$product->is_featured"/>
            <x-admin.toggle name="is_best_seller" label="Best seller" :checked="$product->is_best_seller"/>
            <x-admin.toggle name="is_new" label="New arrival" :checked="$product->is_new"/>
            <button class="a-btn-primary w-full">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
        </section>
        <section class="a-card space-y-4 p-5">
            <x-admin.field name="category_id" type="select" label="Primary category" :value="$product->category_id" :options="collect(['' => '— None —'])->union($categories)"/>
            <div>
                <span class="a-label">Also show in</span>
                <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-line p-3">
                    @php $extra = old('extra_categories', $product->exists ? $product->categories->pluck('id')->all() : []); @endphp
                    @foreach ($categories as $id => $name)
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="extra_categories[]" value="{{ $id }}" @checked(in_array($id, $extra))> {{ $name }}</label>
                    @endforeach
                </div>
            </div>
            <x-admin.field name="brand_id" type="select" label="Brand" :value="$product->brand_id" :options="collect(['' => '— None —'])->union($brands)"/>
        </section>
        @if ($product->exists)
            <section class="a-card p-5 text-xs text-muted">
                <p>Sold: <strong class="text-ink">{{ $product->sold_count }}</strong> · Rating: <strong class="text-ink">{{ number_format($product->rating_avg, 1) }}</strong> ({{ $product->rating_count }})</p>
                <p class="mt-1">Updated {{ $product->updated_at->diffForHumans() }}</p>
            </section>
        @endif
    </aside>
</form>
@if ($product->exists)
    @foreach ($product->images as $img)<form id="del-img-{{ $img->id }}" method="POST" action="{{ route('admin.products.images.destroy', $img) }}">@csrf @method('DELETE')</form>@endforeach
    <div class="mt-6 flex justify-end"><x-admin.delete :action="route('admin.products.destroy', $product)" label="Delete this product" class="a-btn-danger"/></div>
@endif
@endsection

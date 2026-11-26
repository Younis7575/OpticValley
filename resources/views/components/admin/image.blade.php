@props(['name', 'label' => 'Image', 'current' => null, 'help' => 'JPG, PNG, WebP or SVG. Max 4 MB.'])
<div x-data="{ preview: @js($current) }">
    <span class="a-label">{{ $label }}</span>
    <label class="group relative flex aspect-[16/10] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-line bg-paper text-xs text-muted hover:border-brand">
        <img x-show="preview" :src="preview" class="absolute inset-0 size-full object-cover" alt="">
        <span x-show="!preview">Click to upload</span>
        <span x-show="preview" class="absolute bottom-2 right-2 rounded-lg bg-white/90 px-2 py-1 text-[11px] font-semibold text-ink opacity-0 group-hover:opacity-100">Replace</span>
        <input type="file" name="{{ $name }}" accept="image/*" class="sr-only" @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)">
    </label>
    <p class="a-help">{{ $help }}</p>
    @if ($current)<label class="mt-1 flex items-center gap-2 text-[11px] text-muted"><input type="checkbox" name="remove_{{ $name }}" value="1"> Remove current image</label>@endif
    @error($name)<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror
</div>

@props(['name', 'label' => null, 'value' => null, 'rows' => 12, 'required' => false])
<div x-data="{
    wrap(open, close) {
        const t = this.$refs.ta, s = t.selectionStart, e = t.selectionEnd, sel = t.value.slice(s, e) || 'text';
        t.setRangeText(open + sel + close, s, e, 'end'); t.focus();
    },
    link() { const url = prompt('Link URL (https://… or /page)'); if (url) this.wrap(`<a href=&quot;${url}&quot;>`, '</a>'); },
    preview: false,
}">
    @if ($label)<span class="a-label">{{ $label }} @if ($required)<span class="text-sale">*</span>@endif</span>@endif
    <div class="overflow-hidden rounded-xl border border-line bg-white focus-within:border-brand focus-within:ring-4 focus-within:ring-brand/10">
        <div class="flex flex-wrap items-center gap-0.5 border-b border-line bg-paper/60 px-2 py-1.5 text-xs font-semibold">
            <button type="button" @click="wrap('<strong>', '</strong>')" class="rounded px-2 py-1 font-bold hover:bg-white">B</button>
            <button type="button" @click="wrap('<em>', '</em>')" class="rounded px-2 py-1 italic hover:bg-white">I</button>
            <button type="button" @click="wrap('<h2>', '</h2>')" class="rounded px-2 py-1 hover:bg-white">H2</button>
            <button type="button" @click="wrap('<h3>', '</h3>')" class="rounded px-2 py-1 hover:bg-white">H3</button>
            <button type="button" @click="wrap('<p>', '</p>')" class="rounded px-2 py-1 hover:bg-white">¶</button>
            <button type="button" @click="wrap('<ul>\n  <li>', '</li>\n</ul>')" class="rounded px-2 py-1 hover:bg-white">• List</button>
            <button type="button" @click="link()" class="rounded px-2 py-1 hover:bg-white">Link</button>
            <button type="button" @click="preview = !preview" class="ml-auto rounded px-2 py-1 hover:bg-white" x-text="preview ? 'Edit' : 'Preview'"></button>
        </div>
        <textarea x-show="!preview" x-ref="ta" name="{{ $name }}" rows="{{ $rows }}" @required($required) class="block w-full resize-y px-3.5 py-3 font-mono text-[13px] leading-relaxed outline-none">{{ old($name, $value) }}</textarea>
        <div x-show="preview" x-cloak class="prose-admin min-h-40 px-4 py-3 text-sm leading-relaxed [&_h2]:mb-2 [&_h2]:text-xl [&_h2]:font-bold [&_h3]:mb-1 [&_h3]:font-bold [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-3" x-html="$refs.ta.value.replace(/<script[\s\S]*?<\/script>/gi, '')"></div>
    </div>
    <p class="a-help">Basic formatting is supported. Scripts and unsafe attributes are removed automatically on save.</p>
    @error($name)<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror
</div>

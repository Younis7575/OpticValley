@props(['name', 'label', 'checked' => false, 'help' => null])
<label class="flex cursor-pointer items-start gap-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked)) class="peer sr-only">
    <span class="relative mt-0.5 h-5 w-9 shrink-0 rounded-full bg-line transition after:absolute after:left-0.5 after:top-0.5 after:size-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:bg-brand peer-checked:after:translate-x-4"></span>
    <span class="text-sm"><span class="font-semibold">{{ $label }}</span>@if ($help)<span class="block text-[11px] text-muted">{{ $help }}</span>@endif</span>
</label>

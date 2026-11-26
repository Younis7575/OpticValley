@props(['action', 'confirm' => 'Delete this item? This cannot be undone.', 'label' => 'Delete'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" class="inline">
    @csrf @method('DELETE')
    <button {{ $attributes->merge(['class' => 'text-xs font-semibold text-sale hover:underline']) }}>{{ $label }}</button>
</form>

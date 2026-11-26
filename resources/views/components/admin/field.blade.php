@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'help' => null, 'required' => false, 'options' => null, 'rows' => 4, 'placeholder' => null])
@php
    $id = 'f_'.str_replace(['[', ']', '.'], '_', $name);
    $dot = str_replace(['[', ']'], ['.', ''], $name);
    $val = old($dot, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)<label for="{{ $id }}" class="a-label">{{ $label }} @if ($required)<span class="text-sale">*</span>@endif</label>@endif
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @required($required) placeholder="{{ $placeholder }}" {{ $attributes->except('class') }} class="a-input">{{ $val }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" @required($required) {{ $attributes->except('class') }} class="a-input">
            @foreach ($options as $k => $o)<option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $o }}</option>@endforeach
        </select>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $val }}" @required($required) placeholder="{{ $placeholder }}" {{ $attributes->except('class') }} class="a-input">
    @endif
    @if ($help)<p class="a-help">{{ $help }}</p>@endif
    @error($dot)<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror
</div>

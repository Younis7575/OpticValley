<div class="grid grid-cols-2 gap-3">
    <input name="label" value="{{ $a->label ?? 'Home' }}" placeholder="Label (Home, Office)" class="field col-span-2 !py-2.5">
    <input name="name" value="{{ $a->name ?? auth()->user()->name }}" required placeholder="Full name" class="field !py-2.5">
    <input name="phone" value="{{ $a->phone ?? auth()->user()->phone }}" required placeholder="Mobile" class="field !py-2.5">
    <input name="line1" value="{{ $a->line1 ?? '' }}" required placeholder="Address" class="field col-span-2 !py-2.5">
    <input name="line2" value="{{ $a->line2 ?? '' }}" placeholder="Apartment, landmark" class="field col-span-2 !py-2.5">
    <input name="city" value="{{ $a->city ?? '' }}" required placeholder="City" class="field !py-2.5">
    <input name="province" value="{{ $a->province ?? '' }}" placeholder="Province" class="field !py-2.5">
    <input name="postal_code" value="{{ $a->postal_code ?? '' }}" placeholder="Postal code" class="field !py-2.5">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1" @checked($a->is_default ?? false) class="accent-ink"> Default</label>
</div>
@if ($errors->any())<p class="text-xs text-sale">{{ $errors->first() }}</p>@endif

@extends('layouts.store')
@section('title', 'Checkout')
@section('noindex', true)

@php $u = auth()->user(); $provinces = ['Punjab', 'Sindh', 'Khyber Pakhtunkhwa', 'Balochistan', 'Islamabad Capital Territory', 'Gilgit-Baltistan', 'Azad Kashmir']; @endphp

@section('content')
<section class="container-x py-12 md:py-16">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-4xl md:text-5xl">Checkout</h1>
        @guest<p class="text-sm text-muted">Have an account? <a href="{{ route('login', ['intended' => '/checkout']) }}" class="font-semibold text-ink underline underline-offset-4">Sign in</a> for faster checkout.</p>@endguest
    </div>

    @if ($errors->has('cart'))<div class="mt-6 rounded-2xl bg-sale/10 px-5 py-4 text-sm font-semibold text-sale">{{ $errors->first('cart') }} <a href="{{ route('cart.index') }}" class="underline">Review cart</a></div>@endif

    <form action="{{ route('checkout.store') }}" method="POST" class="mt-10 grid gap-10 lg:grid-cols-[1fr_420px]" x-data="{ busy: false }" @submit="busy = true">
        @csrf
        <div class="space-y-8">
            <fieldset class="card p-6 md:p-8">
                <legend class="sr-only">Contact</legend>
                <h2 class="flex items-center gap-3 text-2xl"><span class="grid size-8 place-items-center rounded-full gold-grad font-sans text-sm text-ink">1</span> Contact</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label class="label" for="name">Full name</label><input id="name" name="name" value="{{ old('name', $address->name ?? $u?->name) }}" required autocomplete="name" class="field">@error('name')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
                    <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $u?->email) }}" required autocomplete="email" class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
                    <div><label class="label" for="phone">Mobile number</label><input id="phone" type="tel" name="phone" value="{{ old('phone', $address->phone ?? $u?->phone) }}" required placeholder="03XX XXXXXXX" autocomplete="tel" class="field">@error('phone')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
                </div>
            </fieldset>

            <fieldset class="card p-6 md:p-8">
                <h2 class="flex items-center gap-3 text-2xl"><span class="grid size-8 place-items-center rounded-full gold-grad font-sans text-sm text-ink">2</span> Delivery address</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label class="label" for="line1">Address</label><input id="line1" name="line1" value="{{ old('line1', $address->line1 ?? '') }}" required placeholder="House #, street, area" autocomplete="address-line1" class="field">@error('line1')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label class="label" for="line2">Apartment, landmark (optional)</label><input id="line2" name="line2" value="{{ old('line2', $address->line2 ?? '') }}" autocomplete="address-line2" class="field"></div>
                    <div><label class="label" for="city">City</label><input id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required autocomplete="address-level2" class="field">@error('city')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
                    <div><label class="label" for="province">Province</label>
                        <select id="province" name="province" class="field">
                            <option value="">Select</option>
                            @foreach ($provinces as $p)<option @selected(old('province', $address->province ?? '') === $p)>{{ $p }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="label" for="postal_code">Postal code (optional)</label><input id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" autocomplete="postal-code" class="field"></div>
                    <div class="sm:col-span-2"><label class="label" for="notes">Order notes (optional)</label><textarea id="notes" name="notes" rows="3" placeholder="Delivery instructions, preferred time to call…" class="field">{{ old('notes') }}</textarea></div>
                    @auth
                        @unless ($address)<label class="flex items-center gap-2 text-sm sm:col-span-2"><input type="checkbox" name="save_address" value="1" checked class="accent-ink"> Save this address to my account</label>@endunless
                    @endauth
                </div>
            </fieldset>

            <fieldset class="card p-6 md:p-8">
                <h2 class="flex items-center gap-3 text-2xl"><span class="grid size-8 place-items-center rounded-full gold-grad font-sans text-sm text-ink">3</span> Payment</h2>
                <div class="mt-6 space-y-3" x-data="{ pm: '{{ old('payment_method', array_key_first($methods)) }}' }">
                    @foreach ($methods as $key => [$label, $desc])
                        <label class="flex cursor-pointer items-start gap-4 rounded-2xl border p-5 transition" :class="pm === '{{ $key }}' ? 'border-ink bg-sand/40' : 'border-line hover:border-ink/40'">
                            <input type="radio" name="payment_method" value="{{ $key }}" x-model="pm" class="mt-1 accent-ink">
                            <span class="flex-1">
                                <span class="block font-semibold">{{ $label }}</span>
                                <span class="mt-0.5 block text-xs text-muted">{{ $desc }}</span>
                                @if ($key === 'bank_transfer' && setting('payment_bank_details'))
                                    <span x-show="pm === 'bank_transfer'" x-collapse class="mt-3 block whitespace-pre-line rounded-xl bg-white p-4 text-xs leading-relaxed text-ink-2">{{ setting('payment_bank_details') }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                    @error('payment_method')<p class="text-xs text-sale">{{ $message }}</p>@enderror
                </div>
            </fieldset>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6 md:p-7">
                <h2 class="text-2xl">Your order</h2>
                <div class="mt-6 max-h-[340px] space-y-5 overflow-y-auto pr-1">
                    @foreach ($lines as $line)@include('store.checkout._line', ['line' => $line, 'badge' => true])@endforeach
                </div>
                <div class="mt-6 border-t border-line pt-6">@include('store.checkout._summary')</div>
                <button class="btn-primary mt-6 w-full !py-4" :disabled="busy">
                    <span x-show="!busy">Place order · {{ money($cart->total()) }}</span>
                    <span x-show="busy" x-cloak>Placing your order…</span>
                </button>
                <p class="mt-4 flex items-center justify-center gap-2 text-[11px] text-muted"><x-icon name="shield" class="size-4"/> Your details are encrypted and secure.</p>
                <p class="mt-2 text-center text-[11px] text-muted">By placing your order you agree to our <a href="{{ route('page.show', 'terms-conditions') }}" class="underline">terms</a>.</p>
            </div>
        </aside>
    </form>
</section>
@endsection

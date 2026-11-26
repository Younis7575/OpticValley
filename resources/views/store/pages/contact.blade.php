@extends('layouts.store')
@section('title', 'Contact us')
@section('description', 'Visit OpticValley in Gulberg III, Lahore, call us or message on WhatsApp. We reply within one working day.')
@section('content')
<section class="bg-sand border-b border-line/70"><div class="container-x py-14 md:py-20"><p class="eyebrow">We're here to help</p><h1 class="mt-2 text-4xl md:text-6xl">Get in <em class="italic text-brand">touch</em>.</h1></div></section>
<section class="container-x grid gap-10 py-14 lg:grid-cols-[1fr_1.3fr]">
    <div class="space-y-4">
        @foreach ([['pin', 'Visit our store', setting('store_address'), null], ['phone', 'Call us', setting('store_phone'), 'tel:'.preg_replace('/\s/', '', setting('store_phone'))], ['whatsapp', 'WhatsApp', 'Chat with an optician', 'https://wa.me/'.preg_replace('/\D/', '', setting('whatsapp_number'))], ['mail', 'Email', setting('store_email'), 'mailto:'.setting('store_email')]] as [$ic, $t, $v, $href])
            <div class="card flex items-start gap-4 p-6">
                <span class="grid size-12 shrink-0 place-items-center rounded-full bg-brand-50 text-brand"><x-icon :name="$ic"/></span>
                <div><p class="font-semibold">{{ $t }}</p>@if ($href)<a href="{{ $href }}" class="text-sm text-muted hover:text-ink" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener" @endif>{{ $v }}</a>@else<p class="text-sm text-muted">{{ $v }}</p>@endif</div>
            </div>
        @endforeach
        <p class="px-2 text-sm text-muted">{{ setting('store_hours') }}</p>
    </div>
    <form method="POST" action="{{ route('contact.send') }}" class="card space-y-4 p-6 md:p-9">
        @csrf
        <h2 class="text-2xl">Send us a message</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="label" for="c-name">Name</label><input id="c-name" name="name" value="{{ old('name') }}" required class="field">@error('name')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label" for="c-email">Email</label><input id="c-email" type="email" name="email" value="{{ old('email') }}" required class="field">@error('email')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
            <div><label class="label" for="c-phone">Phone (optional)</label><input id="c-phone" name="phone" value="{{ old('phone') }}" class="field"></div>
            <div><label class="label" for="c-subject">Subject</label><input id="c-subject" name="subject" value="{{ old('subject') }}" class="field"></div>
        </div>
        <div><label class="label" for="c-msg">Message</label><textarea id="c-msg" name="message" rows="5" required class="field">{{ old('message') }}</textarea>@error('message')<p class="mt-1 text-xs text-sale">{{ $message }}</p>@enderror</div>
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
        <button class="btn-primary">Send message</button>
    </form>
</section>
@endsection

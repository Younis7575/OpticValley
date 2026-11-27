@extends('layouts.store')
@section('title', 'Virtual Try-On — see frames on your face')
@section('description', 'Try OpticValley frames on your own face using your camera or a photo. Processed privately on your device.')
@section('content')
<section class="relative overflow-hidden bg-[#101113] text-white">
    <div class="pointer-events-none absolute -right-40 -top-40 size-[520px] rounded-full bg-[#b69a70]/20 blur-[110px]"></div>
    <div class="container-x relative grid gap-10 py-20 md:py-24 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="eyebrow !text-[#e8d3a2]">Virtual Try-On</p>
            <h1 class="mt-3 text-5xl leading-[1.04] md:text-6xl">See it on you,<br><em class="italic text-gold-grad">before you buy.</em></h1>
            <p class="mt-5 max-w-md text-white/65">Use your camera or upload a photo — our frames follow your face in real time. Everything runs on your device; nothing is uploaded or stored.</p>
            @if ($frames->isNotEmpty())
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('tryon:open'))" class="btn-brand mt-8 px-8 py-4"><x-icon name="eye" class="size-4"/> Start Virtual Try-On</button>
            @endif
        </div>
        <ol class="grid gap-3 text-sm text-white/75">
            @foreach (['Tap “Start” and allow camera access — or upload a front-facing photo.', 'Pick a frame; it is placed and scaled to your face automatically.', 'Switch frames, save a photo, then shop the one you love.'] as $i => $step)
                <li class="flex gap-4 rounded-2xl border border-white/10 bg-white/[.03] p-5"><span class="font-display text-2xl text-gold-grad">0{{ $i + 1 }}</span>{{ $step }}</li>
            @endforeach
        </ol>
    </div>
</section>
<section class="container-x py-16">
    <h2 class="text-3xl">Frames available for try-on <span class="text-muted">({{ $frames->count() }})</span></h2>
    @if ($frames->isEmpty())
        <p class="mt-4 text-muted">Virtual Try-On frames are being prepared. Please check back soon.</p>
    @else
        <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-4">
            @foreach ($frames as $p)<x-product-card :product="$p"/>@endforeach
        </div>
    @endif
    <p class="mt-10 max-w-2xl text-xs leading-relaxed text-muted">Privacy: the camera starts only after you press Start. Video and photos are processed in your browser to detect face landmarks and are never sent to OpticValley. The face-tracking model file is downloaded from Google MediaPipe. Try-on is a visual preview; actual size and fit may vary.</p>
</section>
@include('store.partials.tryon-modal')
@endsection

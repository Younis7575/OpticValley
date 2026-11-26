@extends('layouts.store')
@section('title', 'Page not found')
@section('noindex', true)
@section('content')
<section class="container-x grid min-h-[60vh] place-items-center py-20 text-center">
    <div>
        <p class="font-display text-[8rem] leading-none text-ink/10">404</p>
        <h1 class="-mt-6 text-4xl">This page is out of focus.</h1>
        <p class="mt-3 text-muted">The page you're looking for doesn't exist or has moved.</p>
        <div class="mt-8 flex justify-center gap-3"><a href="{{ route('home') }}" class="btn-primary">Go home</a><a href="{{ route('shop') }}" class="btn-outline">Shop all</a></div>
    </div>
</section>
@endsection

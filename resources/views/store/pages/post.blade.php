@extends('layouts.store')
@section('title', $post->meta_title ?: $post->title)
@section('description', $post->meta_description ?: $post->excerpt)
@section('og_type', 'article')
@if ($post->image_url)@section('og_image', $post->image_url)@endif
@push('head')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $post->title, 'datePublished' => $post->published_at->toIso8601String(), 'dateModified' => $post->updated_at->toIso8601String(), 'image' => $post->image_url, 'author' => ['@type' => 'Organization', 'name' => setting('store_name')]], JSON_UNESCAPED_SLASHES) !!}</script>
@endpush
@section('content')
<article>
    <header class="container-x max-w-3xl pt-12 text-center md:pt-20">
        <x-breadcrumbs :items="['Journal' => route('blog.index'), $post->title => null]"/>
        <p class="eyebrow mt-8">{{ $post->published_at->format('F d, Y') }}</p>
        <h1 class="mt-3 text-4xl leading-tight md:text-[3.4rem]">{{ $post->title }}</h1>
        @if ($post->excerpt)<p class="mx-auto mt-5 max-w-xl text-lg text-muted">{{ $post->excerpt }}</p>@endif
    </header>
    @if ($post->image_url)<div class="container-x mt-12 max-w-5xl"><img src="{{ $post->image_url }}" alt="" class="aspect-[16/8] w-full rounded-[2rem] object-cover"></div>@endif
    <div class="container-x max-w-3xl py-14"><div class="prose-ov text-base">{!! $post->body !!}</div></div>
</article>
@if ($more->isNotEmpty())
<section class="container-x pb-20"><h2 class="mb-8 text-3xl">Keep reading</h2>
    <div class="grid gap-6 md:grid-cols-3">@foreach ($more as $p)<a href="{{ route('blog.show', $p) }}" class="group"><div class="aspect-[16/10] overflow-hidden rounded-[1.4rem] bg-sand"><img src="{{ $p->image_url ?? asset('images/placeholder.svg') }}" alt="" loading="lazy" class="size-full object-cover transition duration-700 group-hover:scale-105"></div><h3 class="mt-3 text-xl"><span class="link-u">{{ $p->title }}</span></h3></a>@endforeach</div>
</section>
@endif
@endsection

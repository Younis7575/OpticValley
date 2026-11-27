@extends('layouts.store')
@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@section('content')
<section class="bg-white border-b border-[#ecebe7]"><div class="container-x max-w-4xl py-14 md:py-20"><x-breadcrumbs :items="[$page->title => null]"/><h1 class="mt-4 text-4xl md:text-6xl">{{ $page->title }}</h1></div></section>
<article class="container-x max-w-3xl py-14"><div class="prose-ov">{!! $page->content !!}</div></article>
@endsection

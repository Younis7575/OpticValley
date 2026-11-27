@extends('layouts.store')
@section('title', 'The Journal — eye care, style & lens guides')
@section('description', 'Guides on choosing frames, lens technology, blue light and sunglasses from the OpticValley opticians.')
@section('content')
<section class="bg-white border-b border-[#ecebe7]"><div class="container-x py-14 md:py-20"><p class="eyebrow">The journal</p><h1 class="mt-2 text-4xl md:text-6xl">Eye-care, <em class="italic text-brand">explained</em>.</h1></div></section>
<section class="container-x py-14">
    <div class="grid gap-x-6 gap-y-12 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <a href="{{ route('blog.show', $post) }}" class="group">
                <div class="aspect-[16/10] overflow-hidden rounded-[1.6rem] bg-sand"><img src="{{ $post->image_url ?? asset('images/placeholder.svg') }}" alt="" loading="lazy" class="size-full object-cover transition duration-700 group-hover:scale-105"></div>
                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.18em] text-muted">{{ $post->published_at->format('M d, Y') }}</p>
                <h2 class="mt-1.5 text-2xl leading-snug"><span class="link-u">{{ $post->title }}</span></h2>
                @if ($post->excerpt)<p class="mt-2 text-sm text-muted">{{ $post->excerpt }}</p>@endif
            </a>
        @empty
            <p class="text-muted">Articles are coming soon.</p>
        @endforelse
    </div>
    <div class="mt-14">{{ $posts->links() }}</div>
</section>
@endsection

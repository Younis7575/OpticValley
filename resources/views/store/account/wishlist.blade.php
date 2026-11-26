@extends('store.account._layout')
@section('title', 'Wishlist')
@section('heading', 'Wishlist')
@section('account')
    @if ($products->isEmpty())
        <div class="card p-14 text-center"><x-icon name="heart" class="mx-auto size-8 text-muted"/><p class="mt-3 text-sm text-muted">Tap the heart on any product to save it here.</p><a href="{{ route('shop') }}" class="btn-primary mt-6">Discover frames</a></div>
    @else
        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 xl:grid-cols-3">
            @foreach ($products as $p)<x-product-card :product="$p" :wishlisted="true"/>@endforeach
        </div>
    @endif
@endsection

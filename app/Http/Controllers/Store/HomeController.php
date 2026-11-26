<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function __invoke()
    {
        $base = fn () => Product::active()->with(['images', 'brand']);

        $sunId = Category::where('slug', 'sunglasses')->first();
        $eyeId = Category::where('slug', 'eyeglasses')->first();

        return view('store.home', [
            'heroes' => Banner::active()->where('position', 'hero')->get(),
            'promos' => Banner::active()->where('position', 'promo')->take(3)->get(),
            'featured' => $base()->where('is_featured', true)->latest()->take(8)->get(),
            'bestSellers' => $base()->where('is_best_seller', true)->orderByDesc('sold_count')->take(8)->get(),
            'newArrivals' => $base()->where('is_new', true)->latest()->take(8)->get(),
            'sunglasses' => $sunId ? $base()->inCategory($sunId)->orderByDesc('sold_count')->take(4)->get() : collect(),
            'eyeglasses' => $eyeId ? $base()->inCategory($eyeId)->orderByDesc('is_featured')->take(4)->get() : collect(),
            'offers' => $base()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->orderByDesc('sold_count')->take(4)->get(),
            'categories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->withCount('products')->take(8)->get(),
            'brands' => Brand::active()->where('is_featured', true)->get(),
            'testimonials' => Review::with('product')->where('is_approved', true)->where('rating', 5)->latest()->take(6)->get(),
            'posts' => Post::published()->take(3)->get(),
            'rating' => ['avg' => (float) Review::where('is_approved', true)->avg('rating'), 'count' => Review::where('is_approved', true)->count()],
            'wishlistIds' => auth()->user()?->wishlist()->pluck('products.id')->all() ?? [],
        ]);
    }
}

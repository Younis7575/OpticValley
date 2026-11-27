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

        $usedSamples = [];

        return view('store.home', [
            'heroes' => Banner::active()->where('position', 'hero')->get(),
            'promos' => Banner::active()->where('position', 'promo')->take(3)->get(),
            'featured' => $base()->where('is_featured', true)->latest()->take(8)->get(),
            'bestSellers' => ($bs = $base()->where('is_best_seller', true)->orderByDesc('sold_count')->take(8)->get())->isNotEmpty() ? $bs : $base()->orderByDesc('sold_count')->orderByDesc('is_featured')->take(8)->get(),
            'newArrivals' => ($na = $base()->where('is_new', true)->latest()->take(8)->get())->isNotEmpty() ? $na : $base()->latest()->take(8)->get(),
            'sunglasses' => $sunId ? $base()->inCategory($sunId)->orderByDesc('sold_count')->take(4)->get() : collect(),
            'eyeglasses' => $eyeId ? $base()->inCategory($eyeId)->orderByDesc('is_featured')->take(4)->get() : collect(),
            'offers' => $base()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->orderByDesc('sold_count')->take(4)->get(),
            'categories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->withCount('products')->take(8)->get(),
            'brands' => Brand::active()->where('is_featured', true)->get(),
            'testimonials' => Review::with('product')->where('is_approved', true)->where('rating', 5)->latest()->take(6)->get(),
            'posts' => Post::published()->take(3)->get(),
            'men' => $base()->where('gender', 'men')->orderByDesc('is_featured')->latest()->take(8)->get(),
            'kids' => $base()->where('gender', 'kids')->latest()->take(8)->get(),
            'shapes' => Product::active()->whereNotNull('frame_shape')->selectRaw('frame_shape, COUNT(*) as c')->groupBy('frame_shape')->orderByDesc('c')->pluck('c', 'frame_shape'),
            'stats' => [
                'products' => Product::active()->count(),
                'brands' => Brand::active()->has('products')->count(),
                'from' => (float) Product::active()->min('price'),
            ],
            'collections' => collect([
                ['Men', route('shop', ['gender' => ['men']]), Product::active()->where('gender', 'men')],
                ['Women', route('shop', ['gender' => ['women']]), Product::active()->where('gender', 'women')],
                ['Kids', route('category.show', 'kids-eyeglasses'), Product::active()->where('gender', 'kids')],
                ['Eyeglasses', route('category.show', 'eyeglasses'), ($c = Category::where('slug', 'eyeglasses')->first()) ? Product::active()->inCategory($c) : null],
                ['Sunglasses', route('category.show', 'sunglasses'), ($c = Category::where('slug', 'sunglasses')->first()) ? Product::active()->inCategory($c) : null],
                ['Blue Light', route('category.show', 'blue-light-blocking-glasses'), ($c = Category::where('slug', 'blue-light-blocking-glasses')->first()) ? Product::active()->inCategory($c) : null],
                ['Contact Lenses', route('category.show', 'contact-lenses'), ($c = Category::where('slug', 'contact-lenses')->first()) ? Product::active()->inCategory($c) : null],
            ])->map(function ($c) use (&$usedSamples) {
                if (! $c[2]) {
                    return null;
                }
                $count = (clone $c[2])->count();
                $sample = $count ? (clone $c[2])->with('primaryImage')->whereNotIn('id', $usedSamples)->orderByDesc('is_featured')->orderByDesc('tryon_enabled')->first() : null;
                if ($sample) {
                    $usedSamples[] = $sample->id;
                }

                return $count ? (object) ['name' => $c[0], 'url' => $c[1], 'count' => $count, 'image' => $sample?->image_url] : null;
            })->filter()->values(),
            'tryOnFrames' => Product::active()->where('tryon_enabled', true)->whereNotNull('tryon_overlay')->with(['images', 'brand'])->take(4)->get(),
            'lensOptions' => \App\Models\LensOption::active()->get(),
            'heroCopy' => Banner::active()->where('position', 'hero')->first(),
            'spotlight' => $base()->where('is_featured', true)->inRandomOrder()->first(),
            'rating' => ['avg' => (float) Review::where('is_approved', true)->avg('rating'), 'count' => Review::where('is_approved', true)->count()],
            'wishlistIds' => auth()->user()?->wishlist()->pluck('products.id')->all() ?? [],
        ]);
    }
}

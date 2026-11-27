<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\LensOption;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->listing($request, null, null);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category, null);
    }

    public function brand(Request $request, Brand $brand)
    {
        abort_unless($brand->is_active, 404);

        return $this->listing($request, null, $brand);
    }

    private function listing(Request $request, ?Category $category, ?Brand $brand)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|min:0',
            'sort' => 'nullable|in:featured,newest,price_asc,price_desc,popular,rating',
        ]);

        $scope = Product::active();
        if ($category) {
            $scope->inCategory($category);
        }
        if ($brand) {
            $scope->where('brand_id', $brand->id);
        }

        // Facet values come from the current scope so filters never lead to empty pages.
        $facetBase = clone $scope;
        $facets = [
            'brands' => Brand::active()->whereIn('id', (clone $facetBase)->select('brand_id'))->orderBy('name')->get(),
            'shapes' => (clone $facetBase)->whereNotNull('frame_shape')->distinct()->orderBy('frame_shape')->pluck('frame_shape'),
            'types' => (clone $facetBase)->whereNotNull('frame_type')->distinct()->orderBy('frame_type')->pluck('frame_type'),
            'materials' => (clone $facetBase)->whereNotNull('frame_material')->distinct()->orderBy('frame_material')->pluck('frame_material'),
            'colors' => (clone $facetBase)->whereNotNull('frame_color')->distinct()->orderBy('frame_color')->pluck('frame_color'),
            'genders' => (clone $facetBase)->distinct()->pluck('gender'),
            'priceMax' => (int) ceil((float) (clone $facetBase)->max('price')),
        ];

        $query = $scope->with(['images', 'brand']);
        $priceExpr = 'COALESCE(CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price END, price)';

        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
                $w->where('name', 'like', $like)->orWhere('sku', 'like', $like)->orWhere('frame_shape', 'like', $like)
                    ->orWhere('frame_color', 'like', $like)->orWhere('short_description', 'like', $like)
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like))
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
            });
        }
        foreach (['brand' => 'brand_id', 'shape' => 'frame_shape', 'type' => 'frame_type', 'material' => 'frame_material', 'color' => 'frame_color', 'gender' => 'gender'] as $param => $col) {
            $vals = array_filter((array) $request->input($param, []), fn ($v) => is_scalar($v) && $v !== '');
            if ($vals) {
                $query->whereIn($col, $vals);
            }
        }
        if ($request->filled('min')) {
            $query->whereRaw("$priceExpr >= ?", [(float) $request->min]);
        }
        if ($request->filled('max')) {
            $query->whereRaw("$priceExpr <= ?", [(float) $request->max]);
        }
        if ($request->boolean('sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->boolean('rx')) {
            $query->where('prescription_enabled', true);
        }

        match ($request->input('sort', 'featured')) {
            'newest' => $query->latest(),
            'price_asc' => $query->orderByRaw("$priceExpr asc"),
            'price_desc' => $query->orderByRaw("$priceExpr desc"),
            'popular' => $query->orderByDesc('sold_count'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            default => $query->orderByDesc('is_featured')->orderByDesc('sold_count')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('store.shop.index', [
            'products' => $products,
            'category' => $category?->load(['children' => fn ($q) => $q->where('is_active', true), 'parent']),
            'brand' => $brand,
            'facets' => $facets,
            'wishlistIds' => auth()->user()?->wishlist()->pluck('products.id')->all() ?? [],
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['images', 'brand', 'category.parent', 'variants', 'approvedReviews']);

        $related = Product::active()->with(['images', 'brand'])->whereKeyNot($product->id)
            ->where(fn ($q) => $q->where('category_id', $product->category_id)->orWhere('frame_shape', $product->frame_shape))
            ->orderByDesc('sold_count')->take(4)->get();

        return view('store.shop.product', [
            'product' => $product,
            'related' => $related,
            'lenses' => $product->prescription_enabled ? LensOption::active()->get() : collect(),
            'inWishlist' => auth()->check() && auth()->user()->wishlist()->whereKey($product->id)->exists(),
            'wishlistIds' => auth()->user()?->wishlist()->pluck('products.id')->all() ?? [],
            'canReview' => true,
            'tryOnAlternatives' => $product->hasTryOn() ? collect() : Product::active()->where('tryon_enabled', true)->whereNotNull('tryon_overlay')->with('primaryImage')->take(4)->get(),
        ]);
    }

    public function quick(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('store.shop.quick', ['product' => $product->load(['images', 'brand', 'variants'])]);
    }

    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q'));
        if (mb_strlen($q) < 2) {
            return ['data' => []];
        }
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], mb_substr($q, 0, 60)).'%';
        $items = Product::active()->with(['primaryImage', 'brand'])
            ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('sku', 'like', $like)->orWhere('frame_shape', 'like', $like)->orWhere('frame_color', 'like', $like)->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like)))
            ->orderByDesc('sold_count')->take(6)->get();

        return ['data' => $items->map(fn ($p) => ['name' => $p->name, 'brand' => $p->brand?->name, 'price' => money($p->final_price), 'image' => $p->image_url, 'url' => route('product.show', $p)])];
    }

    public function review(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:120',
            'body' => 'required|string|min:10|max:2000',
        ]);
        $product->reviews()->create($data + ['user_id' => $request->user()?->id, 'is_approved' => false]);

        return back()->with('success', 'Thank you! Your review will appear once approved.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories()
    {
        return ['data' => Category::active()->whereNull('parent_id')->orderBy('sort_order')
            ->with(['children' => fn ($q) => $q->where('is_active', true)])->get()
            ->map(fn ($c) => ['name' => $c->name, 'slug' => $c->slug, 'image' => $c->image_url,
                'children' => $c->children->map(fn ($ch) => ['name' => $ch->name, 'slug' => $ch->slug])])];
    }

    public function brands()
    {
        return ['data' => Brand::active()->orderBy('name')->get(['name', 'slug', 'logo', 'description'])];
    }

    public function products(Request $request)
    {
        $request->validate(['per_page' => 'nullable|integer|min:1|max:60', 'q' => 'nullable|string|max:100']);
        $q = Product::active()->with(['images', 'brand', 'category']);
        if ($c = $request->query('category')) {
            $cat = Category::where('slug', $c)->first();
            $cat ? $q->inCategory($cat) : $q->whereRaw('1 = 0');
        }
        if ($b = $request->query('brand')) {
            $q->whereHas('brand', fn ($w) => $w->where('slug', $b));
        }
        if ($s = $request->query('q')) {
            $q->where('name', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $s).'%');
        }
        foreach (['gender', 'frame_shape', 'frame_material'] as $f) {
            if ($v = $request->query($f)) {
                $q->where($f, $v);
            }
        }

        return ProductResource::collection($q->latest()->paginate($request->integer('per_page', 20))->withQueryString());
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);

        return new ProductResource($product->load(['images', 'brand', 'category', 'variants']));
    }

    public function settings()
    {
        return ['data' => collect(['store_name', 'store_phone', 'store_email', 'store_address', 'currency_code', 'currency_symbol', 'shipping_flat_rate', 'free_shipping_threshold'])
            ->mapWithKeys(fn ($k) => [$k => setting($k)])];
    }
}

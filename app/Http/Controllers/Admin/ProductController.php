<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Html;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with(['primaryImage', 'category', 'brand']);
        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"));
        }
        if ($c = $request->query('category')) {
            $q->where('category_id', $c);
        }
        if ($b = $request->query('brand')) {
            $q->where('brand_id', $b);
        }
        match ($request->query('stock')) {
            'low' => $q->where('stock', '>', 0)->whereColumn('stock', '<=', 'low_stock_threshold'),
            'out' => $q->where('stock', 0),
            default => null,
        };
        if ($request->query('status') !== null && $request->query('status') !== '') {
            $q->where('is_active', $request->query('status') === 'active');
        }

        return view('admin.products.index', [
            'products' => $q->latest()->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
            'brands' => Brand::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', $this->formData(new Product(['is_active' => true, 'gender' => 'unisex', 'low_stock_threshold' => 5])));
    }

    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($this->payload($request));
            $this->syncRelations($product, $request);

            return $product;
        });
        ActivityLog::record('product.create', $product, "Created product {$product->name}");

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', $this->formData($product->load(['images', 'variants', 'categories'])));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($this->payload($request, $product));
            $this->syncRelations($product, $request);
        });
        ActivityLog::record('product.update', $product, "Updated product {$product->name}");

        return back()->with('success', 'Product saved.');
    }

    public function destroy(Product $product)
    {
        if ($product->hasMany(OrderItem::class)->exists()) {
            $product->update(['is_active' => false]);
            ActivityLog::record('product.archive', $product, "Archived product {$product->name} (has orders)");

            return back()->with('success', 'This product has orders, so it was hidden from the store instead of deleted.');
        }
        foreach ($product->images as $img) {
            Media::delete($img->path);
        }
        ActivityLog::record('product.delete', $product, "Deleted product {$product->name}");
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function destroyImage(ProductImage $image)
    {
        Media::delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'categories' => Category::with('parent')->orderBy('parent_id')->orderBy('sort_order')->get()->mapWithKeys(fn ($c) => [$c->id => ($c->parent ? $c->parent->name.' › ' : '').$c->name]),
            'brands' => Brand::orderBy('name')->pluck('name', 'id'),
        ];
    }

    private function payload(ProductRequest $request, ?Product $product = null): array
    {
        $data = $request->safe()->except(['images', 'variants', 'extra_categories', 'spec_keys', 'spec_values', 'image_order', 'image_alt']);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['description'] = Html::clean($data['description'] ?? '');
        $specs = [];
        foreach ($request->input('spec_keys', []) as $i => $k) {
            if (trim((string) $k) !== '') {
                $specs[trim($k)] = (string) ($request->input('spec_values')[$i] ?? '');
            }
        }
        $data['specifications'] = $specs ?: null;
        foreach (['prescription_enabled', 'is_featured', 'is_best_seller', 'is_new', 'is_active'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        return $data;
    }

    private function syncRelations(Product $product, ProductRequest $request): void
    {
        $product->categories()->sync(array_values(array_diff((array) $request->input('extra_categories', []), [$product->category_id])));

        foreach ($request->file('images', []) as $i => $file) {
            $product->images()->create(['path' => Media::store($file, 'products'), 'alt' => $product->name, 'sort_order' => 100 + $i]);
        }
        foreach ((array) $request->input('image_order', []) as $id => $order) {
            $product->images()->whereKey($id)->update(['sort_order' => (int) $order, 'alt' => $request->input("image_alt.$id") ?: $product->name]);
        }

        $keep = [];
        foreach ((array) $request->input('variants', []) as $v) {
            if (empty($v['name'])) {
                continue;
            }
            $attrs = ['name' => $v['name'], 'color_hex' => $v['color_hex'] ?? null, 'sku' => ($v['sku'] ?? null) ?: null, 'price_adjustment' => (float) ($v['price_adjustment'] ?? 0), 'stock' => (int) ($v['stock'] ?? 0)];
            $variant = ! empty($v['id']) ? $product->variants()->find($v['id']) : null;
            $variant ? $variant->update($attrs) : $variant = $product->variants()->create($attrs);
            $keep[] = $variant->id;
        }
        $product->variants()->whereNotIn('id', $keep)->delete();

        if ($keep) {
            // Product-level stock mirrors the sum of its variants.
            $product->update(['stock' => (int) $product->variants()->sum('stock')]);
        }
    }
}

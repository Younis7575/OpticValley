<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::withCount('products')->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%$s%"))->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.form', ['brand' => new Brand(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $b = Brand::create($this->validated($request));
        ActivityLog::record('brand.create', $b, "Created brand {$b->name}");

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->update($this->validated($request, $brand));
        ActivityLog::record('brand.update', $brand, "Updated brand {$brand->name}");

        return redirect()->route('admin.brands.index')->with('success', 'Brand saved.');
    }

    public function destroy(Brand $brand)
    {
        Media::delete($brand->logo);
        ActivityLog::record('brand.delete', $brand, "Deleted brand {$brand->name}");
        $brand->delete();

        return back()->with('success', 'Brand deleted.');
    }

    private function validated(Request $request, ?Brand $brand = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'max:140', Rule::unique('brands')->ignore($brand?->id)],
            'description' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|max:2048',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        if ($request->hasFile('logo')) {
            Media::delete($brand?->logo);
            $data['logo'] = Media::store($request->file('logo'), 'brands');
        } elseif ($request->boolean('remove_logo')) {
            Media::delete($brand?->logo);
            $data['logo'] = null;
        } else {
            unset($data['logo']);
        }

        return $data;
    }
}

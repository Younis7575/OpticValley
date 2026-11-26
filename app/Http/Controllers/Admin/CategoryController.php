<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $tree = Category::whereNull('parent_id')->with(['children' => fn ($q) => $q->withCount('products')])->withCount('products')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('tree'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true, 'show_in_menu' => true]), 'parents' => $this->parents()]);
    }

    public function store(Request $request)
    {
        $c = Category::create($this->validated($request));
        ActivityLog::record('category.create', $c, "Created category {$c->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', ['category' => $category, 'parents' => $this->parents($category)]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));
        ActivityLog::record('category.update', $category, "Updated category {$category->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Category saved.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Move or delete the sub-categories first.');
        }
        Media::delete($category->image);
        ActivityLog::record('category.delete', $category, "Deleted category {$category->name}");
        $category->delete();

        return back()->with('success', 'Category deleted. Its products are now uncategorised.');
    }

    private function parents(?Category $except = null)
    {
        return Category::whereNull('parent_id')->when($except, fn ($q) => $q->whereKeyNot($except->id))->orderBy('name')->pluck('name', 'id')->prepend('— None (top level) —', '');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'max:140', Rule::unique('categories')->ignore($category?->id)],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category?->id])],
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:4096',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_menu'] = $request->boolean('show_in_menu');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['parent_id'] = ($data['parent_id'] ?? null) ?: null;
        if ($request->hasFile('image')) {
            Media::delete($category?->image);
            $data['image'] = Media::store($request->file('image'), 'categories');
        } elseif ($request->boolean('remove_image')) {
            Media::delete($category?->image);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        return $data;
    }
}

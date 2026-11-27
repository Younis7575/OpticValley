<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesCategories;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Support\Media;
use Illuminate\Http\Request;

/** Main (top-level) categories. Subcategories live in SubcategoryController. */
class CategoryController extends Controller
{
    use SavesCategories;

    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%$s%"))
            ->withCount(['children', 'products'])
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true, 'show_in_menu' => true])]);
    }

    public function store(Request $request)
    {
        $c = Category::create($this->categoryData($request, null, false));
        ActivityLog::record('category.create', $c, "Created category {$c->name}");

        return redirect()->route('admin.categories.index')->with('success', "Category “{$c->name}” created. Now add its subcategories.");
    }

    public function edit(Category $category)
    {
        if ($category->parent_id) {
            return redirect()->route('admin.subcategories.edit', $category);
        }

        return view('admin.categories.form', ['category' => $category->loadCount('children')]);
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->parent_id, 404);
        $category->update($this->categoryData($request, $category, false));
        ActivityLog::record('category.update', $category, "Updated category {$category->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Category saved.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->parent_id, 404);
        if ($category->children()->exists()) {
            return back()->with('error', "“{$category->name}” still has subcategories. Delete or move them first.");
        }
        Media::delete($category->image);
        ActivityLog::record('category.delete', $category, "Deleted category {$category->name}");
        $category->delete();

        return back()->with('success', 'Category deleted. Its products are now uncategorised.');
    }
}

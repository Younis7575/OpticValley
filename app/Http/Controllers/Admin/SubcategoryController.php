<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesCategories;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Support\Media;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    use SavesCategories;

    public function index(Request $request)
    {
        $subs = Category::whereNotNull('parent_id')->with('parent')->withCount('products')
            ->when($request->parent, fn ($q, $p) => $q->where('parent_id', $p))
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%$s%"))
            ->orderBy('parent_id')->orderBy('sort_order')->paginate(30)->withQueryString();

        return view('admin.subcategories.index', ['subs' => $subs, 'parents' => $this->parents()]);
    }

    public function create(Request $request)
    {
        $parents = $this->parents();
        if ($parents->isEmpty()) {
            return redirect()->route('admin.categories.create')->with('error', 'Create a main category first, then add subcategories to it.');
        }

        return view('admin.subcategories.form', [
            'sub' => new Category(['is_active' => true, 'show_in_menu' => true, 'parent_id' => $request->integer('parent') ?: null]),
            'parents' => $parents,
        ]);
    }

    public function store(Request $request)
    {
        $s = Category::create($this->categoryData($request, null, true));
        ActivityLog::record('subcategory.create', $s, "Created subcategory {$s->name}");
        $next = $request->boolean('add_another')
            ? redirect()->route('admin.subcategories.create', ['parent' => $s->parent_id])
            : redirect()->route('admin.subcategories.index', ['parent' => $s->parent_id]);

        return $next->with('success', "Subcategory “{$s->name}” added to {$s->parent->name}.");
    }

    public function edit(Category $subcategory)
    {
        if (! $subcategory->parent_id) {
            return redirect()->route('admin.categories.edit', $subcategory);
        }

        return view('admin.subcategories.form', ['sub' => $subcategory, 'parents' => $this->parents()]);
    }

    public function update(Request $request, Category $subcategory)
    {
        abort_unless($subcategory->parent_id, 404);
        $subcategory->update($this->categoryData($request, $subcategory, true));
        ActivityLog::record('subcategory.update', $subcategory, "Updated subcategory {$subcategory->name}");

        return redirect()->route('admin.subcategories.index', ['parent' => $subcategory->parent_id])->with('success', 'Subcategory saved.');
    }

    public function destroy(Category $subcategory)
    {
        abort_unless($subcategory->parent_id, 404);
        Media::delete($subcategory->image);
        ActivityLog::record('subcategory.delete', $subcategory, "Deleted subcategory {$subcategory->name}");
        $subcategory->delete();

        return back()->with('success', 'Subcategory deleted. Its products are now uncategorised.');
    }

    private function parents()
    {
        return Category::whereNull('parent_id')->orderBy('sort_order')->pluck('name', 'id');
    }
}

<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Category;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

trait SavesCategories
{
    /** Shared validation for categories (parent = null) and subcategories (parent required, top-level only). */
    protected function categoryData(Request $request, ?Category $category, bool $isSub): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'max:140', Rule::unique('categories')->ignore($category?->id)],
            'parent_id' => $isSub
                ? ['required', Rule::exists('categories', 'id')->whereNull('parent_id'), Rule::notIn([$category?->id])]
                : ['prohibited'],
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:4096',
        ], [
            'parent_id.required' => 'Please choose the main category this subcategory belongs to.',
            'parent_id.exists' => 'Subcategories can only be placed under a main category.',
            'slug.unique' => 'This URL is already used by another category or subcategory.',
        ]);
        $data['parent_id'] = $isSub ? (int) $data['parent_id'] : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_menu'] = $request->boolean('show_in_menu');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
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

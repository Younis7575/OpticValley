<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Support\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $p = Page::create($this->validated($request));
        ActivityLog::record('page.create', $p, "Created page {$p->title}");

        return redirect()->route('admin.pages.edit', $p)->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));
        ActivityLog::record('page.update', $page, "Updated page {$page->title}");

        return back()->with('success', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title', ''))]);
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'slug' => ['required', 'max:160', Rule::unique('pages')->ignore($page?->id)],
            'content' => 'nullable|string|max:100000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $data['content'] = Html::clean($data['content'] ?? '');

        return $data + ['is_active' => $request->boolean('is_active')];
    }
}

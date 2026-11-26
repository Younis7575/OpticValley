<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Banner;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', ['banners' => Banner::orderBy('position')->orderBy('sort_order')->get()->groupBy('position')]);
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true, 'position' => request('position', 'hero'), 'theme' => 'light'])]);
    }

    public function store(Request $request)
    {
        $b = Banner::create($this->validated($request));
        ActivityLog::record('banner.create', $b, "Created banner {$b->title}");

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($this->validated($request, $banner));
        ActivityLog::record('banner.update', $banner, "Updated banner {$banner->title}");

        return redirect()->route('admin.banners.index')->with('success', 'Banner saved.');
    }

    public function destroy(Banner $banner)
    {
        Media::delete($banner->image);
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }

    private function validated(Request $request, ?Banner $banner = null): array
    {
        $data = $request->validate([
            'position' => ['required', Rule::in(array_keys(Banner::POSITIONS))],
            'eyebrow' => 'nullable|string|max:80',
            'title' => 'required|string|max:160',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:40',
            'link' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https?:\/\/)/'],
            'theme' => 'required|in:light,dark',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
        ], ['link.regex' => 'Links must start with / or https://']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if ($request->hasFile('image')) {
            Media::delete($banner?->image);
            $data['image'] = Media::store($request->file('image'), 'banners');
        } elseif ($request->boolean('remove_image')) {
            Media::delete($banner?->image);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        return $data;
    }
}

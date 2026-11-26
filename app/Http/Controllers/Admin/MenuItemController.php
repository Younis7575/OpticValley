<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index()
    {
        return view('admin.menus.index', ['groups' => MenuItem::orderBy('sort_order')->get()->groupBy('location')]);
    }

    public function create()
    {
        return view('admin.menus.form', ['item' => new MenuItem(['is_active' => true, 'location' => request('location', 'header')])]);
    }

    public function store(Request $request)
    {
        MenuItem::create($this->validated($request));

        return redirect()->route('admin.menus.index')->with('success', 'Menu link added.');
    }

    public function edit(MenuItem $menuItem)
    {
        return view('admin.menus.form', ['item' => $menuItem]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $menuItem->update($this->validated($request));

        return redirect()->route('admin.menus.index')->with('success', 'Menu link saved.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return back()->with('success', 'Menu link removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'location' => ['required', Rule::in(array_keys(MenuItem::LOCATIONS))],
            'label' => 'required|string|max:60',
            'url' => ['required', 'string', 'max:255', 'regex:/^(\/|https?:\/\/)/'],
            'sort_order' => 'nullable|integer|min:0',
        ], ['url.regex' => 'Links must start with / or https://']) + ['is_active' => $request->boolean('is_active'), 'sort_order' => (int) $request->input('sort_order', 0)];
    }
}

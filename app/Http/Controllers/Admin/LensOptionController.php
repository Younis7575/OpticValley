<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LensOption;
use Illuminate\Http\Request;

class LensOptionController extends Controller
{
    public function index()
    {
        return view('admin.lens-options.index', ['lenses' => LensOption::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.lens-options.form', ['lens' => new LensOption(['is_active' => true, 'requires_prescription' => true])]);
    }

    public function store(Request $request)
    {
        $l = LensOption::create($this->validated($request));
        ActivityLog::record('lens.create', $l, "Created lens option {$l->name}");

        return redirect()->route('admin.lens-options.index')->with('success', 'Lens option created.');
    }

    public function edit(LensOption $lensOption)
    {
        return view('admin.lens-options.form', ['lens' => $lensOption]);
    }

    public function update(Request $request, LensOption $lensOption)
    {
        $lensOption->update($this->validated($request));
        ActivityLog::record('lens.update', $lensOption, "Updated lens option {$lensOption->name}");

        return redirect()->route('admin.lens-options.index')->with('success', 'Lens option saved.');
    }

    public function destroy(LensOption $lensOption)
    {
        $lensOption->delete();

        return back()->with('success', 'Lens option deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]) + ['is_active' => $request->boolean('is_active'), 'requires_prescription' => $request->boolean('requires_prescription')];
    }
}

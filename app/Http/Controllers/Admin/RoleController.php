<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', ['roles' => Role::withCount('users')->get()]);
    }

    public function create()
    {
        return view('admin.roles.form', ['role' => new Role(['permissions' => []])]);
    }

    public function store(Request $request)
    {
        $role = Role::create($this->validated($request));
        ActivityLog::record('role.create', $role, "Created role {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.form', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'The Super Admin role always has full access and cannot be changed.');
        }
        $role->update($this->validated($request, $role));
        ActivityLog::record('role.update', $role, "Updated role {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role saved.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin' || $role->users()->exists()) {
            return back()->with('error', 'This role is protected or still assigned to users.');
        }
        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('roles')->ignore($role?->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => Rule::in(array_keys(Role::PERMISSIONS)),
        ]);

        return ['name' => $data['name'], 'slug' => $role?->slug ?? Str::slug($data['name']), 'permissions' => array_values($data['permissions'] ?? [])];
    }
}

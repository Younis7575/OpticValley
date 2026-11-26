<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::whereNotNull('role_id')->with('role')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => Role::pluck('name', 'id')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => ['required', Password::min(10)->letters()->numbers()],
        ]);
        $user = User::create($data + ['is_active' => $request->boolean('is_active'), 'email_verified_at' => now()]);
        ActivityLog::record('user.create', $user, "Created admin user {$user->email}");

        return redirect()->route('admin.users.index')->with('success', 'Admin user created.');
    }

    public function edit(User $user)
    {
        abort_if($user->role_id === null, 404);

        return view('admin.users.form', ['user' => $user, 'roles' => Role::pluck('name', 'id')]);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role_id === null, 404);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'password' => ['nullable', Password::min(10)->letters()->numbers()],
        ]);
        if ($user->is(auth()->user()) && (! $request->boolean('is_active') || (int) $data['role_id'] !== $user->role_id)) {
            return back()->with('error', 'You cannot deactivate yourself or change your own role.');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data + ['is_active' => $request->boolean('is_active')]);
        ActivityLog::record('user.update', $user, "Updated admin user {$user->email}");

        return redirect()->route('admin.users.index')->with('success', 'Admin user saved.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role_id === null, 404);
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        ActivityLog::record('user.delete', $user, "Deleted admin user {$user->email}");
        $user->delete();

        return back()->with('success', 'Admin user deleted.');
    }
}

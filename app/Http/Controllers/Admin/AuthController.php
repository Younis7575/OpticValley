<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function show()
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid credentials.']);
        }
        if (! $request->user()->isAdmin()) {
            Auth::logout();

            return back()->withInput($request->only('email'))->withErrors(['email' => 'This account does not have admin access.']);
        }
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();
        ActivityLog::record('auth.login', $request->user(), 'Signed in to admin');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function profile(Request $request)
    {
        return view('admin.auth.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();
        ActivityLog::record('profile.update', $user, 'Updated own profile');

        return back()->with('success', 'Profile updated.');
    }
}

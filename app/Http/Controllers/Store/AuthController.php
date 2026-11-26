<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->filled('intended') && str_starts_with($request->intended, '/')) {
            session(['url.intended' => url($request->intended)]);
        }

        return view('store.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'These credentials do not match our records.']);
        }
        $cart = session('cart.items');
        $request->session()->regenerate();
        if ($cart) {
            session(['cart.items' => $cart]);
        }
        $request->user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('account.dashboard'))->with('success', 'Welcome back, '.Str::before($request->user()->name, ' ').'!');
    }

    public function showRegister()
    {
        return view('store.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160|unique:users,email',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{10,20}$/'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);
        $user = User::create($data + ['is_active' => true]);
        Auth::login($user, true);

        return redirect()->intended(route('account.dashboard'))->with('success', 'Your account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgot()
    {
        return view('store.auth.forgot');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));

        // Same response whether or not the email exists, to avoid account enumeration.
        return back()->with('success', 'If an account exists for that email, a reset link is on its way.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('store.auth.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset. Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }
}

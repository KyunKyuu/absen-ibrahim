<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $identifier = $field === 'username' ? strtolower($data['login']) : $data['login'];
        $credentials = [$field => $identifier, 'password' => $data['password']];

        $remember = $request->boolean('remember');

        if (Auth::attempt([...$credentials, 'is_active' => true], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login' => 'Username/email atau password tidak cocok.',
        ])->onlyInput('login');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Password berhasil diperbarui.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

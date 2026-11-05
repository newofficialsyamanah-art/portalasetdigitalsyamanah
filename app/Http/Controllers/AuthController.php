<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember', true));
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => (string)$user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'role' => $user->role,
                    ]
                ]);
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah!'], 401);
        }

        return back()->withErrors(['username' => 'Username atau password salah!'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem.');
    }

    public function switchRole(Request $request)
    {
        $role = $request->input('role');
        $user = User::where('role', $role)->first() ?? User::role($role)->first();
        if ($user) {
            Auth::login($user);
            return back()->with('success', 'Beralih ke peran ' . strtoupper(str_replace('_', ' ', $role)));
        }
        return back()->with('error', 'Pengguna dengan peran tersebut tidak ditemukan.');
    }
}


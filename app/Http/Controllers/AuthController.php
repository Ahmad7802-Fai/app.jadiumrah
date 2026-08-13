<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN FORM
    |--------------------------------------------------------------------------
    */
    public function showLogin()
    {
        // Jika sudah login → langsung lempar ke dashboard role
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }



    /*
    |--------------------------------------------------------------------------
    | PROCESS LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Ambil credential
        $credentials = $request->only('email', 'password');

        // Proses login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 🔥 Redirect otomatis lewat route universal dashboard
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }



    /*
    |--------------------------------------------------------------------------
    | PROCESS LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('jamaah')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('jamaah.dashboard');
        }

        return back()->withErrors([
            'email' => 'Login jamaah gagal',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('jamaah')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

// namespace App\Http\Controllers\Jamaah;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use App\Models\JamaahUser;

// class AuthController extends Controller
// {
//     /**
//      * FORM LOGIN
//      */
//     public function showLogin()
//     {
//         return view('jamaah.auth.login');
//     }

//     /**
//      * PROSES LOGIN (EMAIL / HP)
//      */
//     public function login(Request $request)
//     {
//         $request->validate([
//             'login'    => 'required',
//             'password' => 'required',
//         ]);

//         $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
//             ? 'email'
//             : 'phone';

//         $credentials = [
//             $loginField => $request->login,
//             'password'  => $request->password,
//             'is_active' => 1,
//         ];

//         if (Auth::guard('jamaah')->attempt($credentials)) {

//             $request->session()->regenerate();

//             auth('jamaah')->user()
//                 ->update(['last_login_at' => now()]);
//                 return redirect()->route('jamaah.dashboard');
//         }

//         return back()->withErrors([
//             'login' => 'Email / No HP atau password salah',
//         ]);
//     }

//     /**
//      * LOGOUT
//      */
//     public function logout(Request $request)
//     {
//         auth('jamaah')->logout();

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }

// }

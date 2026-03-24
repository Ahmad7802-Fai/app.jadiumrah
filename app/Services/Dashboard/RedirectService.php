<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RedirectService
{
    public function redirectPath(): string
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if (!Route::has('dashboard')) {
            abort(404, 'Dashboard route not defined.');
        }

        return route('dashboard');
    }
}
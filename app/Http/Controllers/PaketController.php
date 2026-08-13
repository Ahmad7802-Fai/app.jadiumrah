<?php

namespace App\Http\Controllers;

use App\Models\PaketUmrah;

class PaketController extends Controller
{
    public function show($slug)
{
    dd(session('referral'));
}
    // public function show(string $slug)
    // {
    //     $paket = PaketUmrah::where('slug', $slug)
    //         ->where('is_active', 1)
    //         ->firstOrFail();

    //     return view('paket.show', compact('paket'));
    // }
}

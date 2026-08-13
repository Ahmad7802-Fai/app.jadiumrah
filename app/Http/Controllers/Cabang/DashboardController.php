<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardFactory;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
public function index()
{
    $user = auth()->user();

    $dashboard = \App\Services\Dashboard\DashboardFactory::make($user);

    $month = now()->month;
    $year  = now()->year;

    // ⬇️ INI KUNCI UTAMA
    $cardsData = $dashboard->cards($month, $year);
    $chartData = $dashboard->chart($month, $year);

    return view('cabang.dashboard.index', [
        // ⚠️ WAJIB cards saja, BUKAN title
        'cards' => $cardsData['cards'],

        'statusChart' => [
            'lunas' => $chartData['thisMonth'][0] ?? 0,
            'belum' => $chartData['lastMonth'][0] ?? 0,
        ],

        'agentsChart' => [], // nanti kalau mau ditambah
    ]);
}


}

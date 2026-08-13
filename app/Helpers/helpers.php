<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\TabunganMonthlyClosing;

/* =========================================================
 | FORMAT TANGGAL INDONESIA
========================================================= */
if (! function_exists('tanggal_indo')) {
    function tanggal_indo($date, bool $withDay = false): string
    {
        if (! $date) {
            return '-';
        }

        $date = $date instanceof Carbon
            ? $date
            : Carbon::parse($date);

        Carbon::setLocale('id');

        return $withDay
            ? $date->translatedFormat('l, d F Y')
            : $date->translatedFormat('d F Y');
    }
}

/* =========================================================
 | TABUNGAN — MONTHLY LOCK
========================================================= */
if (! function_exists('tabunganBulanLocked')) {
    function tabunganBulanLocked(Carbon $tanggal): bool
    {
        return TabunganMonthlyClosing::where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->exists();
    }
}

/* =========================================================
 | AUTH — REDIRECT BY GUARD
========================================================= */
if (! function_exists('redirect_by_auth')) {
    function redirect_by_auth()
    {
        if (Auth::guard('jamaah')->check()) {
            return redirect()->route('jamaah.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect('/dashboard');
        }

        return redirect()->route('login');
    }
}

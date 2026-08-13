<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\TabunganMonthlyClosing;

if (!function_exists('tabunganBulanLocked')) {
    function tabunganBulanLocked(Carbon $tanggal): bool
    {
        return TabunganMonthlyClosing::where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->exists();
    }
}


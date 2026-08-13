<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\TabunganUmrah;
use App\Models\JamaahNotification;
use App\Models\JamaahPublic;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('jamaah')->user();

        if (!$user) {
            return redirect()->route('jamaah.login');
        }

        $jamaah = JamaahPublic::with([
                'paket',
                'keberangkatan',
                'tabungan',
            ])
            ->find($user->jamaah_id);

        if (!$jamaah || !$user->is_active) {
            return view('jamaah.waiting');
        }

        $tabungan = $jamaah->tabungan;

        $notifUnread = JamaahNotification::where('jamaah_id', $jamaah->id)
            ->where('is_read', 0)
            ->count();
        
        // ================== PEMBAYARAN ==================
        $hargaPaket = $jamaah->harga_disepakati
            ?? $jamaah->harga_default
            ?? 0;

        $totalBayar = $tabungan?->saldo ?? 0;

        $sisaPembayaran = max(0, $hargaPaket - $totalBayar);

        $progressPembayaran = $hargaPaket > 0
            ? min(100, round(($totalBayar / $hargaPaket) * 100))
            : 0;


        return view('jamaah.dashboard', [
            'jamaah'              => $jamaah,
            'tabungan'            => $tabungan,
            'notifUnread'         => $notifUnread,
            'hargaPaket'          => $hargaPaket,
            'totalBayar'          => $totalBayar,
            'sisaPembayaran'      => $sisaPembayaran,
            'progressPembayaran'  => $progressPembayaran,
        ]);

    }
}

// namespace App\Http\Controllers\Jamaah;

// use App\Http\Controllers\Controller;
// use App\Models\TabunganUmrah;
// use App\Models\JamaahNotification;
// use App\Models\JamaahPublic;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $user = auth('jamaah')->user();

//         if (!$user) {
//             return redirect()->route('jamaah.login');
//         }

//         // ✅ PAKAI MODEL PUBLIC (TANPA GLOBAL SCOPE)
//         $jamaah = JamaahPublic::find($user->jamaah_id);

//         if (!$jamaah || !$user->is_active) {
//             return view('jamaah.waiting');
//         }

//         $tabungan = TabunganUmrah::where('jamaah_id', $jamaah->id)
//             ->where('status', 'ACTIVE')
//             ->first();

//         $notifUnread = JamaahNotification::where('jamaah_id', $jamaah->id)
//             ->where('is_read', 0)
//             ->count();

//         return view('jamaah.dashboard', compact(
//             'jamaah',
//             'tabungan',
//             'notifUnread'
//         ));
//     }
// }


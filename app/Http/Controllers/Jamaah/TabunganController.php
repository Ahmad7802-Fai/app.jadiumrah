<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\TabunganUmrah;
use App\Models\TabunganTopup;
use App\Models\TabunganTransaksi;
use App\Models\JamaahNotification;

use App\Services\WhatsAppService;

class TabunganController extends Controller
{
    /* ======================================================
     | INTERNAL HELPER
     | Pastikan tabungan ACTIVE selalu tersedia
     ====================================================== */
    protected function getOrCreateTabungan($jamaah)
    {
        return TabunganUmrah::firstOrCreate(
            [
                'jamaah_id' => $jamaah->id,
                'status'    => 'ACTIVE',
            ],
            [
                'nomor_tabungan' => 'TAB-' . str_pad($jamaah->id, 4, '0', STR_PAD_LEFT),
                'nama_tabungan'  => 'Tabungan Umrah ' . $jamaah->nama_lengkap,
                'target_nominal' => 0,
                'saldo'          => 0,
            ]
        );
    }

    /* ======================================================
     | DASHBOARD TABUNGAN
     ====================================================== */
    public function index()
    {
        $jamaah   = auth('jamaah')->user()->jamaah;
        $tabungan = $this->getOrCreateTabungan($jamaah);

        // Transaksi terakhir
        $transaksi = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Hitung saldo dari transaksi (aman)
        $totalSaldo = $transaksi->sum('amount');

        // Pending top up
        $hasPendingTopup = TabunganTopup::where('jamaah_id', $jamaah->id)
            ->where('status', 'PENDING')
            ->exists();

        return view('jamaah.tabungan.index', compact(
            'jamaah',
            'tabungan',
            'transaksi',
            'hasPendingTopup',
            'totalSaldo'
        ));
    }

    /* ======================================================
     | FORM TOP UP
     ====================================================== */
    public function createTopup()
    {
        $jamaah   = auth('jamaah')->user()->jamaah;
        $tabungan = $this->getOrCreateTabungan($jamaah);

        // Cegah double pending
        $hasPending = TabunganTopup::where('jamaah_id', $jamaah->id)
            ->where('status', 'PENDING')
            ->exists();

        if ($hasPending) {
            return redirect()
                ->route('jamaah.tabungan.index')
                ->with('warning', 'Masih ada top up yang belum diverifikasi.');
        }

        return view('jamaah.tabungan.topup', compact(
            'jamaah',
            'tabungan'
        ));
    }

    /* ======================================================
     | STORE TOP UP (PENDING)
     ====================================================== */
    public function storeTopup(Request $request)
{
    Log::info('[JAMAAH] STORE TOPUP START');

    $request->validate([
        'amount'        => 'required|numeric|min:100000',
        'transfer_date' => 'required|date',
        'bank_sender'   => 'required|string|max:100',
        'proof_file'    => 'required|image|max:3072',
    ]);

    $jamaah   = auth('jamaah')->user()->jamaah;
    $tabungan = $this->getOrCreateTabungan($jamaah);

    // Cegah double pending
    if (
        TabunganTopup::where('jamaah_id', $jamaah->id)
            ->where('status', 'PENDING')
            ->exists()
    ) {
        return redirect()
            ->route('jamaah.tabungan.index')
            ->with('warning', 'Masih ada top up yang belum diverifikasi.');
    }

    DB::transaction(function () use ($request, $jamaah, $tabungan) {

        $path = $request->file('proof_file')
            ->store('tabungan/topup', 'public');

        $topup = TabunganTopup::create([
            'tabungan_id'   => $tabungan->id,
            'jamaah_id'     => $jamaah->id,
            'amount'        => $request->amount,
            'transfer_date' => $request->transfer_date,
            'bank_sender'   => $request->bank_sender,
            'bank_receiver' => 'Rekening Umrah',
            'proof_file'    => $path,
            'status'        => 'PENDING',
        ]);

        // 🔔 Notifikasi jamaah (IN-APP)
        JamaahNotification::create([
            'jamaah_id' => $jamaah->id,
            'title'     => 'Top Up Menunggu Verifikasi',
            'message'   => 'Top up sebesar Rp '
                . number_format($topup->amount, 0, ',', '.')
                . ' telah diterima dan menunggu verifikasi admin.',
            'is_read'   => 0,
        ]);
    });

    return redirect()
        ->route('jamaah.tabungan.index')
        ->with('success', 'Top up berhasil dikirim dan menunggu verifikasi admin.');
}


    /* ======================================================
     | RIWAYAT TOP UP
     ====================================================== */
    public function history()
    {
        $jamaah = auth('jamaah')->user()->jamaah;

        $topups = TabunganTopup::where('jamaah_id', $jamaah->id)
            ->latest()
            ->paginate(10);

        return view('jamaah.tabungan.topup-history', compact(
            'jamaah',
            'topups'
        ));
    }
}

<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\Invoices;
use App\Models\BiayaOperasional;
use App\Models\BiayaKeberangkatan;
use App\Models\LayananTransaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // 1. Statistik Pembayaran
        // ===============================
        $totalPembayaran = Payments::where('status', 'valid')->sum('jumlah');
        $pendingPembayaran = Payments::where('status', 'pending')->sum('jumlah');
        $ditolakPembayaran = Payments::where('status', 'ditolak')->sum('jumlah');

        // ===============================
        // 2. Invoice
        // ===============================
        $invoiceBelum = Invoices::where('status', 'belum_lunas')->count();
        $invoiceCicilan = Invoices::where('status', 'cicilan')->count();
        $invoiceMenunggu = Invoices::where('status', 'menunggu_validasi')->count();
        $invoiceLunas = Invoices::where('status', 'lunas')->count();

        // ===============================
        // 3. Operasional & Pengeluaran
        // ===============================
        $totalOperasional = BiayaOperasional::sum('jumlah');
        $totalKeberangkatan = BiayaKeberangkatan::sum('jumlah');

        // ===============================
        // 4. Transaksi Layanan
        // ===============================
        $pendapatanLayanan = LayananTransaksi::sum('total');

        // ===============================
        // 5. Grafik pemasukan 12 bulan terakhir
        // ===============================
        $chartMonths = [];
        $chartValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('M Y');

            $sum = Payments::where('status', 'valid')
                ->whereMonth('tanggal_bayar', Carbon::now()->subMonths($i)->format('m'))
                ->whereYear('tanggal_bayar', Carbon::now()->subMonths($i)->format('Y'))
                ->sum('jumlah');

            $chartMonths[] = $month;
            $chartValues[] = $sum;
        }

        return view('keuangan.dashboard.index', compact(
            'totalPembayaran',
            'pendingPembayaran',
            'ditolakPembayaran',
            'invoiceBelum',
            'invoiceCicilan',
            'invoiceMenunggu',
            'invoiceLunas',
            'totalOperasional',
            'totalKeberangkatan',
            'pendapatanLayanan',
            'chartMonths',
            'chartValues'
        ));
    }
}

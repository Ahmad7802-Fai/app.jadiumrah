<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keberangkatan;
use App\Models\Payments;

class PembayaranJamaahController extends Controller
{
    /* ============================================================
     * INDEX
     * ============================================================ */
    public function index(Request $request)
    {
        // List keberangkatan untuk filter
        $keberangkatan = Keberangkatan::orderBy('tanggal_berangkat', 'ASC')->get();

        // Query dasar pembayaran
        $payments = Payments::with(['jamaah', 'invoice']);

        // Filter keberangkatan
        if ($request->keberangkatan_id) {
            $payments->whereHas('jamaah', function($q) use ($request) {
                $q->where('id_keberangkatan', $request->keberangkatan_id);
            });
        }

        // Search multi-field
        if ($request->search) {
            $s = $request->search;

            $payments->where(function($q) use ($s) {
                $q->where('jumlah', 'like', "%$s%")
                  ->orWhereHas('jamaah', function ($j) use ($s) {
                      $j->where('nama_lengkap', 'like', "%$s%")
                        ->orWhere('nik', 'like', "%$s%")
                        ->orWhere('no_id', 'like', "%$s%");
                  });
            });
        }

        // Pagination
        $payments = $payments->orderBy('tanggal_bayar', 'DESC')->paginate(20);

        return view('keuangan.payments.index', [
            'payments'      => $payments,
            'keberangkatan' => $keberangkatan,
        ]);
    }

    /* ============================================================
     * CREATE
     * ============================================================ */
    public function create()
    {
        return view('keuangan.payments.create');
    }

    /* ============================================================
     * SHOW
     * ============================================================ */
    public function show($id)
    {
        $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

        return view('keuangan.payments.show', compact('payment'));
    }

    /* ============================================================
     * EDIT
     * ============================================================ */
    public function edit($id)
    {
        $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

        return view('keuangan.payments.edit', compact('payment'));
    }

    /* ============================================================
     * STORE / UPDATE / DELETE (placeholder)
     * ============================================================ */

    public function store(Request $req)
    {
        // nanti kita buat premium + validasi + update invoice otomatis
    }

    public function update(Request $req, $id)
    {
        // nanti premium version
    }

    public function destroy($id)
    {
        // nanti premium version
    }
}

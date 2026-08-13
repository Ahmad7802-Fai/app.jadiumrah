<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Payments;
use App\Models\PaymentLogs;

class InvoiceJamaahController extends Controller
{
    /**
     * ============================================================
     * INDEX – DAFTAR INVOICE JAMAAH (ADMIN / KEUANGAN)
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = Invoices::with([
                // ⬅️ PENTING: BYPASS GLOBAL SCOPE JAMAAH
                'jamaah' => fn ($q) => $q->withoutGlobalScopes(),
            ])
            ->latest();

        /* =============================
         | FILTER KEYWORD
         ============================= */
        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($w) use ($q) {
                $w->whereHas('jamaah', function ($j) use ($q) {
                        $j->withoutGlobalScopes()
                          ->where('nama_lengkap', 'like', "%{$q}%")
                          ->orWhere('no_id', 'like', "%{$q}%");
                    })
                  ->orWhere('nomor_invoice', 'like', "%{$q}%");
            });
        }

        /* =============================
         | FILTER STATUS
         ============================= */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query
            ->paginate(15)
            ->withQueryString();

        /* =============================
         | REALTIME CALCULATION
         ============================= */
        foreach ($invoices as $inv) {

            $validPayments = Payments::where('invoice_id', $inv->id)
                ->where('status', 'valid')
                ->where(fn ($q) =>
                    $q->whereNull('is_deleted')
                      ->orWhere('is_deleted', 0)
                )
                ->get();

            $total = $validPayments->sum('jumlah');
            $sisa  = max(0, $inv->total_tagihan - $total);

            // 🔥 override runtime only
            $inv->total_terbayar = $total;
            $inv->sisa_tagihan   = $sisa;
            $inv->status         = $sisa <= 0
                ? 'lunas'
                : ($total > 0 ? 'cicilan' : 'belum_lunas');
        }

        return view('keuangan.invoice-jamaah.index', compact('invoices'));
    }

    /**
     * ============================================================
     * SHOW – DETAIL INVOICE JAMAAH
     * ============================================================
     */
    public function show(int $id)
    {
        $invoice = Invoices::with([
                'jamaah' => fn ($q) => $q->withoutGlobalScopes(),
            ])
            ->findOrFail($id);

        $jamaah = $invoice->jamaah;

        /* =============================
         | PAYMENT HISTORY (VALID)
         ============================= */
        $history = Payments::where('invoice_id', $invoice->id)
            ->where('status', 'valid')
            ->where(fn ($q) =>
                $q->whereNull('is_deleted')
                  ->orWhere('is_deleted', 0)
            )
            ->orderBy('tanggal_bayar')
            ->get();

        $total_terbayar = $history->sum('jumlah');
        $sisa_tagihan   = max(0, $invoice->total_tagihan - $total_terbayar);

        /* =============================
         | PAYMENT LOGS
         ============================= */
        $logs = PaymentLogs::whereIn('payment_id', $history->pluck('id'))
            ->latest()
            ->get();

        return view('keuangan.invoice-jamaah.show', compact(
            'invoice',
            'jamaah',
            'history',
            'logs',
            'total_terbayar',
            'sisa_tagihan'
        ));
    }

    /**
     * ============================================================
     * PRINT – INVOICE PREMIUM PDF
     * ============================================================
     */
    public function printInvoicePremium(int $id)
    {
        $invoice = Invoices::with([
                'jamaah' => fn ($q) => $q->withoutGlobalScopes(),
            ])
            ->findOrFail($id);

        $jamaah = $invoice->jamaah;

        $history = Payments::where('invoice_id', $invoice->id)
            ->where('status', 'valid')
            ->where(fn ($q) =>
                $q->whereNull('is_deleted')
                  ->orWhere('is_deleted', 0)
            )
            ->orderBy('tanggal_bayar')
            ->get();

        $total_terbayar = $history->sum('jumlah');
        $sisa_tagihan   = max(0, $invoice->total_tagihan - $total_terbayar);

        $status = $sisa_tagihan <= 0
            ? 'Lunas'
            : ($total_terbayar > 0 ? 'Cicilan' : 'Belum Lunas');

        $pdf = \PDF::loadView('keuangan.invoice-jamaah.print-premium', [
            'invoice'        => $invoice,
            'jamaah'         => $jamaah,
            'history'        => $history,
            'total_terbayar' => $total_terbayar,
            'sisa_tagihan'   => $sisa_tagihan,
            'status'         => $status,
            'printedAt'      => now()->format('d M Y H:i'),
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("Invoice-{$invoice->nomor_invoice}.pdf");
    }
}

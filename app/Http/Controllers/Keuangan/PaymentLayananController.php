<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LayananInvoice;
use App\Models\LayananPayment;

class PaymentLayananController extends Controller
{
    public function create($id)
    {
        $invoice = LayananInvoice::with('transaksi.client')->findOrFail($id);

        return view('keuangan.invoice-layanan.payment.create', compact('invoice'));
    }


    public function store(Request $request, $id)
    {
        $invoice = LayananInvoice::findOrFail($id);

        /* =====================================================
        1) VALIDASI FORM
        ====================================================== */
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,transfer,qris',
            'bank'           => 'nullable|string|max:100',
            'reference_no'   => 'nullable|string|max:150',
            'catatan'        => 'nullable|string|max:500',
            'proof'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        /* =====================================================
        2) VALIDASI: INVOICE SUDAH LUNAS?
        ====================================================== */
        if ($invoice->paid_amount >= $invoice->amount) {
            return redirect()
                ->back()
                ->with('warning', 'Invoice sudah lunas. Tidak dapat menerima pembayaran lagi.');
        }

        /* =====================================================
        3) VALIDASI: JUMLAH PEMBAYARAN TIDAK BOLEH LEBIH
        ====================================================== */
        $sisa = $invoice->amount - $invoice->paid_amount;

        if ($request->amount > $sisa) {
            return redirect()
                ->back()
                ->withErrors(['amount' => 'Jumlah pembayaran melebihi sisa tagihan (Rp ' . number_format($sisa, 0, ',', '.') . ')'])
                ->withInput();
        }

        /* =====================================================
        4) UPLOAD BUKTI PEMBAYARAN (AMAN)
        ====================================================== */
        $fileName = null;

        if ($request->hasFile('proof')) {
            $fileName = time() . '_' . str_replace(' ', '_', $request->proof->getClientOriginalName());
            $request->proof->move(public_path('uploads/bukti-pembayaran'), $fileName);
        }

        /* =====================================================
        5) SIMPAN PAYMENT
        ====================================================== */
        LayananPayment::create([
            'layanan_invoice_id' => $invoice->id,
            'amount'             => $request->amount,
            'payment_method'     => $request->payment_method,
            'bank'               => $request->bank,
            'reference_no'       => $request->reference_no,
            'catatan'            => $request->catatan,
            'proof_filename'     => $fileName,
            'status'             => 'approved',
            'payer_name'         => $invoice->transaksi->client->nama ?? null, // opsional
        ]);

        /* =====================================================
        6) UPDATE INVOICE
        ====================================================== */
        $invoice->paid_amount += $request->amount;

        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'unpaid';
        }

        $invoice->save();

        /* =====================================================
        7) REDIRECT PREMIUM
        ====================================================== */
        return redirect()
            ->route('keuangan.invoice.show', $invoice->id)
            ->with('success', 'Pembayaran berhasil disimpan.');
    }

}

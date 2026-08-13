<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\LayananTransaksi;
use App\Models\LayananInvoice;
use Illuminate\Http\Request;

class InvoiceLayananController extends Controller
{
    /**
     * INDEX – daftar invoice layanan
     */
    public function index()
    {
        $invoices = LayananInvoice::with([
                'transaksi.client'
            ])
            ->orderBy('id', 'desc')
            ->paginate(30);

        return view('keuangan.invoice-layanan.index', compact('invoices'));
    }



    /**
     * SHOW – detail invoice layanan
     */
    public function show($id)
    {
        $invoice = LayananInvoice::with([
                'transaksi.client',
                'transaksi.items.item',   // relasi item layanan
                'payments'
            ])
            ->findOrFail($id);

        return view('keuangan.invoice-layanan.show', compact('invoice'));
    }


    /**
     * GENERATE – buat invoice dari transaksi layanan
     */
    public function generate($transaksi_id)
    {
        // Ambil transaksi lengkap
        $trx = LayananTransaksi::with(['items.item', 'client'])->findOrFail($transaksi_id);

        // Cegah double invoice
        if ($trx->invoice) {
            return redirect()
                ->route('keuangan.invoice-layanan.show', $trx->invoice->id)
                ->with('warning', 'Invoice untuk transaksi ini sudah dibuat.');
        }

        // Pastikan ada item + subtotal valid
        if ($trx->items->count() < 1) {
            return redirect()
                ->route('keuangan.transaksi-layanan.show', $trx->id)
                ->with('error', 'Transaksi tidak memiliki item layanan.');
        }

        if ($trx->subtotal <= 0) {
            return redirect()
                ->route('keuangan.transaksi-layanan.show', $trx->id)
                ->with('error', 'Subtotal transaksi tidak valid.');
        }

        // Generate nomor invoice (INV/YYYY/MM/00001)
        $last = LayananInvoice::orderBy('id', 'desc')->first();
        $urut = $last ? $last->id + 1 : 1;
        $no_invoice = 'INV/' . date('Y') . '/' . date('m') . '/' . str_pad($urut, 5, '0', STR_PAD_LEFT);

        \DB::beginTransaction();
        try {
            // CREATE INVOICE — PENTING: gunakan 'id_transaksi' sesuai kolom DB
            $invoice = LayananInvoice::create([
                'no_invoice'   => $no_invoice,
                'id_transaksi' => $trx->id,        // <-- FIX: wajib mengisi kolom ini
                'amount'       => $trx->subtotal,
                'paid_amount'  => 0,
                'currency'     => 'IDR',
                'status'       => 'unpaid',
                'due_date'     => now()->addDays(7),
            ]);

            // optional: tandai transaksi sebagai invoiced
            $trx->update(['status' => 'invoiced']);

            \DB::commit();

            return redirect()
                ->route('keuangan.invoice-layanan.show', $invoice->id)
                ->with('success', 'Invoice berhasil dibuat.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    public function createPayment($id)
    {
        $invoice = LayananInvoice::with(['transaksi.client'])->findOrFail($id);

        return view('keuangan.invoice-layanan.payment.create', compact('invoice'));

    }

    public function storePayment(Request $request, $id)
    {
        $invoice = LayananInvoice::with('payments')->findOrFail($id);

        /* ============================================================
        VALIDASI INPUT – PREMIUM F7
        ============================================================ */
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:transfer,cash,qris',
            'bank'           => 'nullable|string|max:50',
            'reference_no'   => 'nullable|string|max:100',
            'catatan'        => 'nullable|string|max:500',
            'proof'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        /* ============================================================
        GENERATE PAYMENT NO (KWITANSI)
        Format: PAY-2025-00023
        ============================================================ */
        $lastPay = $invoice->payments()->orderBy('id', 'desc')->first();
        $urut    = $lastPay ? $lastPay->id + 1 : 1;
        $paymentNo = 'PAY-' . date('Y') . '-' . str_pad($urut, 5, '0', STR_PAD_LEFT);


        /* ============================================================
        UPLOAD BUKTI PEMBAYARAN (Jika Ada)
        ============================================================ */
        $proofPath = null;

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store(
                'uploads/pembayaran-layanan',
                'public'
            );
        }


        /* ============================================================
        SIMPAN PEMBAYARAN
        ============================================================ */
        $payment = $invoice->payments()->create([
            'payment_no'     => $paymentNo,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'bank'           => $request->bank,
            'reference_no'   => $request->reference_no,
            'catatan'        => $request->catatan,
            'proof'          => $proofPath,
            'payment_date'   => now(),
            'status'         => 'pending', // PATCH
        ]);


        /* ============================================================
        UPDATE STATUS INVOICE (REALTIME)
        ============================================================ */
        $totalPaid   = $invoice->payments()->sum('amount');
        $remaining   = $invoice->amount - $totalPaid;

        // Update invoice
        $invoice->paid_amount = $totalPaid;
        $invoice->status = $remaining <= 0
            ? 'paid'
            : ($totalPaid > 0 ? 'partial' : 'unpaid');

        $invoice->save();


        /* ============================================================
        REDIRECT DENGAN FLASH PREMIUM F7
        ============================================================ */
        return redirect()
            ->route('keuangan.invoice-layanan.show', $invoice->id)
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }



    /**
     * PRINT INVOICE — layout premium
     */
    public function print($id)
    {
        // Ambil data invoice layanan + relasi lengkap
        $invoice = LayananInvoice::with([
                'transaksi.client',
                'transaksi.items.item',
                'payments'
            ])
            ->findOrFail($id);

        // --- Jika hanya ingin preview HTML (tanpa PDF) ---
        // return view('keuangan.invoice-layanan.print-premium', compact('invoice'));

        // --- Generate PDF A4 Portrait ---
        $pdf = \PDF::loadView('keuangan.invoice-layanan.print-premium', [
                'invoice' => $invoice
            ])
            ->setPaper('A4', 'portrait');   // ⚡ Set ukuran halaman

        // Tampilkan di browser
        return $pdf->stream("Invoice-Layanan-{$invoice->nomor_invoice}.pdf");
    }
    
    /* ==========================================================
    PATCH APPROVAL — APPROVE PEMBAYARAN
    ========================================================== */
    public function approvePayment($invoice_id, $payment_id)
    {
        $invoice = LayananInvoice::findOrFail($invoice_id);
        $payment = $invoice->payments()->findOrFail($payment_id);

        // Update sebagai approved
        $payment->update([
            'status'         => 'approved',
            'validated_by'   => auth()->id(),
            'validated_at'   => now(),
            'validation_note'=> 'Approved by finance'
        ]);

        // Hitung ulang total paid dari payments approved saja
        $paid = $invoice->payments()->where('status', 'approved')->sum('amount');
        $remaining = $invoice->amount - $paid;

        $invoice->update([
            'paid_amount' => $paid,
            'status'      => $remaining <= 0
                                ? 'paid'
                                : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        return back()->with('success', 'Pembayaran berhasil di-approve.');
    }


    /* ==========================================================
    PATCH APPROVAL — REJECT PEMBAYARAN
    ========================================================== */
    public function rejectPayment(Request $request, $invoice_id, $payment_id)
    {
        $invoice = LayananInvoice::findOrFail($invoice_id);
        $payment = $invoice->payments()->findOrFail($payment_id);

        $payment->update([
            'status'         => 'rejected',
            'validated_by'   => auth()->id(),
            'validated_at'   => now(),
            'validation_note'=> $request->note ?? 'Payment rejected'
        ]);

        // Hitung ulang total paid approved saja
        $paid = $invoice->payments()->where('status','approved')->sum('amount');
        $remaining = $invoice->amount - $paid;

        $invoice->update([
            'paid_amount' => $paid,
            'status'      => $remaining <= 0
                                ? 'paid'
                                : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

}

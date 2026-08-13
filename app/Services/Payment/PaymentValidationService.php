<?php

namespace App\Services\Payment;

use App\Models\Payments;
use App\Models\Invoices;
use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use App\Services\Komisi\KomisiService;
use Exception;

class PaymentValidationService
{
        public function __construct(
        protected KomisiService $komisiService
    ) {}
    /**
     * APPROVE PAYMENT (KEUANGAN / ADMIN)
     * - Validasi payment
     * - Update invoice
     * - Update status jamaah
     * - Buat komisi agent (jika memenuhi syarat)
     */
    public function approve(Payments $payment, int $adminId): Payments
    {
        return DB::transaction(function () use ($payment, $adminId) {

            // 🔒 Lock payment
            $payment = Payments::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== Payments::STATUS_PENDING) {
                throw new Exception('Pembayaran sudah diproses.');
            }

            // 🔒 Lock jamaah
            $jamaah = Jamaah::lockForUpdate()->findOrFail($payment->jamaah_id);

            // ===============================
            // 1️⃣ INVOICE
            // ===============================
            $invoice = Invoices::where('jamaah_id', $jamaah->id)
                ->whereIn('status', ['BELUM_LUNAS','CICILAN'])
                ->lockForUpdate()
                ->first();

            if (!$invoice) {
                $total = (int) $jamaah->harga_disepakati;

                if ($total <= 0) {
                    throw new Exception('Harga jamaah belum ditentukan.');
                }

                $invoice = Invoices::create([
                    'jamaah_id'      => $jamaah->id,
                    'nomor_invoice'  => $this->generateInvoiceNumber(),
                    'tanggal'        => now()->toDateString(),
                    'total_tagihan'  => $total,
                    'total_terbayar' => 0,
                    'sisa_tagihan'   => $total,
                    'status'         => 'BELUM_LUNAS',
                ]);
            }

            if ($payment->jumlah > $invoice->sisa_tagihan) {
                throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
            }

            // ===============================
            // 2️⃣ APPROVE PAYMENT
            // ===============================
            $payment->update([
                'invoice_id'  => $invoice->id,
                'status'      => Payments::STATUS_VALID,
                'approved_by' => $adminId,
                'approved_at' => now(),
            ]);

            // ===============================
            // 3️⃣ RECALCULATE INVOICE
            // ===============================
            $this->recalculateInvoice($invoice);

            // ===============================
            // 4️⃣ UPDATE STATUS JAMAAH
            // ===============================
            if ($jamaah->status === 'pending') {
                $jamaah->update([
                    'status' => 'approved',
                ]);
            }
            // ===============================
            // 5️⃣ KOMISI AGENT (AFTER COMMIT)
            // ===============================
            if ($payment->status === Payments::STATUS_VALID) {
                DB::afterCommit(function () use ($jamaah, $payment) {
                    $this->komisiService->generateFromPayment(
                        $jamaah,
                        $payment
                    );
                });
            }
            return $payment;
        });
    }
    /**
     * HITUNG ULANG INVOICE
     */
    protected function recalculateInvoice(Invoices $invoice): void
    {
        $totalPaid = Payments::where('invoice_id', $invoice->id)
            ->where('status', Payments::STATUS_VALID)
            ->where('is_deleted', 0)
            ->sum('jumlah');

        $sisa = max(0, $invoice->total_tagihan - $totalPaid);

        $invoice->update([
            'total_terbayar' => $totalPaid,
            'sisa_tagihan'   => $sisa,
            'status'         => $sisa === 0
                ? 'LUNAS'
                : ($totalPaid > 0 ? 'CICILAN' : 'BELUM_LUNAS'),
        ]);
    }

    /**
     * GENERATE NOMOR INVOICE
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y') . '-';

        $last = Invoices::where('nomor_invoice', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $num = $last
            ? ((int) substr($last->nomor_invoice, -5) + 1)
            : 1;

        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}

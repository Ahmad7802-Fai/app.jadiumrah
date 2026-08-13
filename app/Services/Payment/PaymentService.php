<?php

namespace App\Services\Payment;

use App\Models\Payments;
use App\Models\Invoices;
use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class PaymentService
{
    /* ============================================================
     | INPUT PAYMENT (AGENT / CABANG / KEUANGAN)
     | STATUS = PENDING
     ============================================================ */
    public function input(array $payload, int $userId): Payments
    {
        return DB::transaction(function () use ($payload, $userId) {

            // ===============================
            // LOCK JAMAAH
            // ===============================
            $jamaah = Jamaah::lockForUpdate()
                ->findOrFail($payload['jamaah_id']);

            // ===============================
            // VALIDASI HARGA
            // ===============================
            $harga = $jamaah->harga_disepakati
                  ?? $jamaah->harga_default
                  ?? 0;

            if ($harga <= 0) {
                throw new Exception('Harga jamaah belum ditentukan.');
            }

            // ===============================
            // UPLOAD BUKTI
            // ===============================
            $buktiPath = null;
            if (!empty($payload['bukti_transfer'])) {
                $buktiPath = $payload['bukti_transfer']
                    ->store('payments', 'public');
            }

            // ===============================
            // CREATE PAYMENT
            // ===============================
            return Payments::create([
                'jamaah_id'      => $jamaah->id,
                'invoice_id'     => null,
                'metode'         => $payload['metode'],
                'tanggal_bayar'  => Carbon::parse($payload['tanggal_bayar'])->startOfDay(),
                'jumlah'         => (int) $payload['jumlah'],
                'keterangan'     => $payload['keterangan'] ?? 'Input pembayaran',
                'bukti_transfer' => $buktiPath,
                'status'         => Payments::STATUS_PENDING,
                'created_by'     => $userId,
            ]);
        });
    }

    /* ============================================================
    | APPROVE PAYMENT (KEUANGAN)
    | - VALIDASI
    | - UPDATE PAYMENT
    | - UPDATE INVOICE
    | - LOGGING
    ============================================================ */
    public function approve(Payments $payment, int $adminId): Payments
    {
        return DB::transaction(function () use ($payment, $adminId) {

            // 🔒 Lock payment
            $payment = Payments::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== Payments::STATUS_PENDING) {
                throw new Exception('Pembayaran sudah diproses.');
            }

            // 🔒 Lock jamaah (tanpa global scope)
            $jamaah = Jamaah::withoutGlobalScopes()
                ->lockForUpdate()
                ->findOrFail($payment->jamaah_id);

            // 🚫 TABUNGAN TIDAK BOLEH APPROVE
            if ($jamaah->tipe_jamaah === 'tabungan') {
                throw new Exception(
                    'Pembayaran jamaah TABUNGAN tidak dapat diproses.'
                );
            }

            // 🔎 Cari invoice aktif
            $invoice = Invoices::where('jamaah_id', $jamaah->id)
                ->whereIn('status', ['belum_lunas', 'cicilan'])
                ->lockForUpdate()
                ->first();

            // 🧾 Buat invoice jika belum ada
            if (! $invoice) {
                $total = (int) $jamaah->harga_akhir;

                if ($total <= 0) {
                    throw new Exception('Harga jamaah belum valid.');
                }

                $invoice = Invoices::create([
                    'jamaah_id'      => $jamaah->id,
                    'nomor_invoice'  => $this->generateInvoiceNumber(),
                    'tanggal'        => now()->toDateString(),
                    'total_tagihan'  => $total,
                    'total_terbayar' => 0,
                    'sisa_tagihan'   => $total,
                    'status'         => 'belum_lunas',
                ]);
            }

            // ❌ Guard overpayment
            if ($payment->jumlah > $invoice->sisa_tagihan) {
                throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
            }

            // ✅ Update payment
            $payment->update([
                'invoice_id'  => $invoice->id,
                'status'      => Payments::STATUS_VALID,
                'validated_by'=> $adminId,
                'validated_at'=> now(),
            ]);

            // 🔁 Recalculate invoice
            $this->recalculateInvoice($invoice);

            // 🧾 Log approve
            app(\App\Services\Payment\PaymentLogService::class)
                ->approve($payment);

            return $payment;
        });
    }


    // /* ============================================================
    //  | APPROVE PAYMENT (KEUANGAN)
    //  ============================================================ */
    // public function approve(Payments $payment, int $adminId): Payments
    // {
    //     return DB::transaction(function () use ($payment, $adminId) {

    //         $payment = Payments::lockForUpdate()->findOrFail($payment->id);

    //         if ($payment->status !== Payments::STATUS_PENDING) {
    //             throw new Exception('Pembayaran sudah diproses.');
    //         }

    //         $jamaah = Jamaah::lockForUpdate()
    //             ->findOrFail($payment->jamaah_id);

    //         // ===============================
    //         // INVOICE
    //         // ===============================
    //         $invoice = Invoices::where('jamaah_id', $jamaah->id)
    //             ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
    //             ->lockForUpdate()
    //             ->first();

    //         if (!$invoice) {
    //             $total = $jamaah->harga_disepakati
    //                   ?? $jamaah->harga_default;

    //             $invoice = Invoices::create([
    //                 'jamaah_id'      => $jamaah->id,
    //                 'nomor_invoice'  => $this->generateInvoiceNumber(),
    //                 'tanggal'        => now()->toDateString(),
    //                 'total_tagihan'  => $total,
    //                 'total_terbayar' => 0,
    //                 'sisa_tagihan'   => $total,
    //                 'status'         => 'BELUM_LUNAS',
    //             ]);
    //         }

    //         if ($payment->jumlah > $invoice->sisa_tagihan) {
    //             throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
    //         }

    //         // ===============================
    //         // VALIDATE PAYMENT
    //         // ===============================
    //         $payment->update([
    //             'invoice_id'  => $invoice->id,
    //             'status'      => Payments::STATUS_VALID,
    //             'approved_by' => $adminId,
    //             'approved_at' => now(),
    //         ]);

    //         $this->recalculateInvoice($invoice);

    //         return $payment;
    //     });
    // }

    /* ============================================================
     | RECALCULATE INVOICE
     ============================================================ */
    private function recalculateInvoice(Invoices $invoice): void
    {
        $totalPaid = Payments::where('invoice_id', $invoice->id)
            ->where('status', Payments::STATUS_VALID)
            ->sum('jumlah');

        $sisa = max(0, $invoice->total_tagihan - $totalPaid);

        $invoice->update([
            'total_terbayar' => $totalPaid,
            'sisa_tagihan'   => $sisa,
            'status' => $sisa === 0
                ? 'LUNAS'
                : ($totalPaid > 0 ? 'CICILAN' : 'BELUM_LUNAS'),
        ]);
    }

    /* ============================================================
     | INVOICE NUMBER
     ============================================================ */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y') . '-';

        $last = Invoices::where('nomor_invoice', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $num = $last
            ? ((int) substr($last->nomor_invoice, -5) + 1)
            : 1;

        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}

// namespace App\Services\Payment;

// use App\Models\Payments;
// use App\Models\Invoices;
// use App\Models\Jamaah;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;
// use Exception;

// class PaymentService
// {
//     /* ============================================================
//      | INPUT PAYMENT (SEMUA ROLE)
//      | - AGENT / CABANG / KEUANGAN
//      | - STATUS = PENDING
//      | - LOGGING VIA OBSERVER
//      ============================================================ */
//     public function input(array $payload, int $userId): Payments
//     {
//         return DB::transaction(function () use ($payload, $userId) {

//             $jamaah = Jamaah::lockForUpdate()
//                 ->findOrFail($payload['jamaah_id']);

//             // 🔒 VALIDASI HARGA
//             if ($jamaah->harga_akhir <= 0) {
//                 throw new Exception('Harga jamaah belum ditentukan.');
//             }

//             // 📎 UPLOAD BUKTI
//             $buktiPath = null;
//             if (!empty($payload['bukti_transfer'])) {
//                 $buktiPath = $payload['bukti_transfer']
//                     ->store('payments', 'public');
//             }

//             // 💾 SIMPAN PAYMENT
//             return Payments::create([
//                 'jamaah_id'      => $jamaah->id,
//                 'invoice_id'     => null,
//                 'metode'         => $payload['metode'],
//                 'tanggal_bayar'  => Carbon::parse($payload['tanggal_bayar'])->startOfDay(),
//                 'jumlah'         => (int) $payload['jumlah'],
//                 'keterangan'     => $payload['keterangan'] ?? 'Input pembayaran',
//                 'bukti_transfer' => $buktiPath,
//                 'status'         => Payments::STATUS_PENDING,
//                 'created_by'     => $userId,
//             ]);
//         });
//     }

//     /* ============================================================
//      | APPROVE PAYMENT (KEUANGAN)
//      | - LOGGING VIA OBSERVER (status berubah)
//      ============================================================ */
//     public function approve(Payments $payment, int $adminId): Payments
//     {
//         return DB::transaction(function () use ($payment, $adminId) {

//             $payment = Payments::lockForUpdate()->findOrFail($payment->id);

//             if ($payment->status !== Payments::STATUS_PENDING) {
//                 throw new Exception('Pembayaran sudah diproses.');
//             }

//             $jamaah = Jamaah::lockForUpdate()
//                 ->findOrFail($payment->jamaah_id);

//             // 🔎 CARI / BUAT INVOICE
//             $invoice = Invoices::where('jamaah_id', $jamaah->id)
//                 ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
//                 ->lockForUpdate()
//                 ->first();

//             if (!$invoice) {
//                 $invoice = Invoices::create([
//                     'jamaah_id'      => $jamaah->id,
//                     'nomor_invoice'  => $this->generateInvoiceNumber(),
//                     'tanggal'        => now()->toDateString(),
//                     'total_tagihan'  => $jamaah->harga_akhir,
//                     'total_terbayar' => 0,
//                     'sisa_tagihan'   => $jamaah->harga_akhir,
//                     'status'         => 'BELUM_LUNAS',
//                 ]);
//             }

//             if ($payment->jumlah > $invoice->sisa_tagihan) {
//                 throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
//             }

//             // ✅ VALIDASI PAYMENT
//             $payment->update([
//                 'invoice_id'  => $invoice->id,
//                 'status'      => Payments::STATUS_VALID,
//                 'approved_by' => $adminId,
//                 'approved_at' => now(),
//             ]);

//             $this->recalculateInvoice($invoice);

//             return $payment;
//         });
//     }

//     /* ============================================================
//      | REJECT PAYMENT
//      | - LOGGING VIA OBSERVER
//      ============================================================ */
//     public function reject(Payments $payment, int $adminId, string $reason): Payments
//     {
//         return DB::transaction(function () use ($payment, $adminId, $reason) {

//             $payment = Payments::lockForUpdate()->findOrFail($payment->id);

//             if ($payment->status !== Payments::STATUS_PENDING) {
//                 throw new Exception('Hanya payment pending yang bisa ditolak.');
//             }

//             $payment->update([
//                 'status'       => Payments::STATUS_REJECTED,
//                 'rejected_by'  => $adminId,
//                 'rejected_at'  => now(),
//                 'reject_note'  => $reason,
//             ]);

//             return $payment;
//         });
//     }

//     /* ============================================================
//      | RECALCULATE INVOICE
//      ============================================================ */
//     private function recalculateInvoice(Invoices $invoice): void
//     {
//         $totalPaid = Payments::where('invoice_id', $invoice->id)
//             ->where('status', Payments::STATUS_VALID)
//             ->sum('jumlah');

//         $sisa = max(0, $invoice->total_tagihan - $totalPaid);

//         $invoice->update([
//             'total_terbayar' => $totalPaid,
//             'sisa_tagihan'   => $sisa,
//             'status' => $sisa === 0
//                 ? 'LUNAS'
//                 : ($totalPaid > 0 ? 'CICILAN' : 'BELUM_LUNAS'),
//         ]);
//     }

//     /* ============================================================
//      | GENERATE INVOICE NUMBER
//      ============================================================ */
//     private function generateInvoiceNumber(): string
//     {
//         $prefix = 'INV-' . date('Y') . '-';

//         $last = Invoices::where('nomor_invoice', 'like', $prefix . '%')
//             ->lockForUpdate()
//             ->orderByDesc('id')
//             ->first();

//         $num = $last
//             ? ((int) substr($last->nomor_invoice, -5) + 1)
//             : 1;

//         return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
//     }
// }

// namespace App\Services\Payment;

// use App\Models\Payments;
// use App\Models\Invoices;
// use App\Models\Jamaah;
// use App\Models\PaymentLogs;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;
// use Exception;

// class PaymentService
// {
//     /* ============================================================
//      | INPUT PAYMENT (SEMUA ROLE)
//      | - TIDAK BUAT INVOICE
//      | - STATUS = pending
//      ============================================================ */
//     public function input(array $payload, int $userId): Payments
//     {
//         return DB::transaction(function () use ($payload, $userId) {

//             $jamaah = Jamaah::lockForUpdate()->findOrFail($payload['jamaah_id']);

//             if ($jamaah->harga_akhir <= 0) {
//                 throw new Exception('Harga jamaah belum ditentukan.');
//             }

//             $buktiPath = null;
//             if (!empty($payload['bukti_transfer'])) {
//                 $buktiPath = $payload['bukti_transfer']->store('payments', 'public');
//             }

//             $payment = Payments::create([
//                 'jamaah_id'      => $jamaah->id,
//                 'invoice_id'     => null,
//                 'metode'         => $payload['metode'],
//                 'tanggal_bayar'  => Carbon::parse($payload['tanggal_bayar'])->startOfDay(),
//                 'jumlah'         => (int) $payload['jumlah'],
//                 'keterangan'     => $payload['keterangan'] ?? 'Input pembayaran',
//                 'bukti_transfer' => $buktiPath,
//                 'status'         => 'PENDING',
//                 'created_by'     => $userId,
//             ]);

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'INPUT',
//                 'meta'       => json_encode(['amount' => $payment->jumlah]),
//                 'created_by' => $userId,
//             ]);

//             return $payment;
//         });
//     }

//     /* ============================================================
//      | APPROVE PAYMENT (KEUANGAN)
//      ============================================================ */
//     public function approve(Payments $payment, int $adminId): Payments
//     {
//         return DB::transaction(function () use ($payment, $adminId) {

//             $payment = Payments::lockForUpdate()->findOrFail($payment->id);

//             if ($payment->status !== 'pending') {
//                 throw new Exception('Pembayaran sudah diproses.');
//             }

//             $jamaah = Jamaah::lockForUpdate()->findOrFail($payment->jamaah_id);

//             $invoice = Invoices::where('jamaah_id', $jamaah->id)
//                 ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
//                 ->lockForUpdate()
//                 ->first();

//             if (!$invoice) {
//                 $total = $jamaah->harga_akhir;

//                 if ($total <= 0) {
//                     throw new Exception('Harga jamaah belum valid.');
//                 }

//                 $invoice = Invoices::create([
//                     'jamaah_id'      => $jamaah->id,
//                     'nomor_invoice'  => $this->generateInvoiceNumber(),
//                     'tanggal'        => now()->toDateString(),
//                     'total_tagihan'  => $total,
//                     'total_terbayar' => 0,
//                     'sisa_tagihan'   => $total,
//                     'status'         => 'BELUM_LUNAS',
//                 ]);
//             }

//             if ($payment->jumlah > $invoice->sisa_tagihan) {
//                 throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
//             }

//             $payment->update([
//                 'invoice_id'  => $invoice->id,
//                 'status'      => 'VALID',
//                 'approved_by' => $adminId,
//                 'approved_at' => now(),
//             ]);

//             $this->recalculateInvoice($invoice);

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'APPROVE',
//                 'meta'       => json_encode([
//                     'invoice' => $invoice->nomor_invoice,
//                     'amount'  => $payment->jumlah,
//                 ]),
//                 'created_by' => $adminId,
//             ]);

//             return $payment;
//         });
//     }

//     /* ============================================================
//      | REJECT PAYMENT
//      ============================================================ */
//     public function reject(Payments $payment, int $adminId, string $reason): void
//     {
//         DB::transaction(function () use ($payment, $adminId, $reason) {

//             $payment = Payments::lockForUpdate()->findOrFail($payment->id);

//             if ($payment->status !== 'PENDING') {
//                 throw new Exception('Hanya payment pending yang bisa ditolak.');
//             }

//             $payment->update([
//                 'status'       => 'REJECTED',
//                 'rejected_by'  => $adminId,
//                 'rejected_at'  => now(),
//             ]);

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'REJECT',
//                 'meta'       => json_encode(['reason' => $reason]),
//                 'created_by' => $adminId,
//             ]);
//         });
//     }

//     /* ============================================================
//      | RECALCULATE INVOICE
//      ============================================================ */
//     private function recalculateInvoice(Invoices $invoice): void
//     {
//         $totalPaid = Payments::where('invoice_id', $invoice->id)
//             ->where('status', 'valid')
//             ->where('is_deleted', 0)
//             ->sum('jumlah');

//         $sisa = max(0, $invoice->total_tagihan - $totalPaid);

//         $invoice->update([
//             'total_terbayar' => $totalPaid,
//             'sisa_tagihan'   => $sisa,
//             'status'         => $sisa === 0
//                 ? 'lunas'
//                 : ($totalPaid > 0 ? 'cicilan' : 'belum_lunas'),
//         ]);
//     }

//     private function generateInvoiceNumber(): string
//     {
//         $prefix = 'INV-' . date('Y') . '-';

//         $last = Invoices::where('nomor_invoice', 'like', $prefix.'%')
//             ->lockForUpdate()
//             ->orderByDesc('id')
//             ->first();

//         $num = $last
//             ? ((int) substr($last->nomor_invoice, -5) + 1)
//             : 1;

//         return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
//     }

//     public function inputFromAgent(
//         int $jamaahId,
//         int $amount,
//         string $type,
//         int $agentId
//     ): Payments {
//         return DB::transaction(function () use ($jamaahId, $amount, $type, $agentId) {

//             // 🔒 Ambil jamaah TANPA global scope
//             $jamaah = Jamaah::withoutGlobalScopes()
//                 ->lockForUpdate()
//                 ->findOrFail($jamaahId);

//             // 🔐 Ownership
//             abort_if(
//                 $jamaah->agent_id !== $agentId,
//                 403,
//                 'Jamaah bukan milik agent'
//             );

//             // 💰 Harga final
//             $harga = (int) $jamaah->harga_disepakati;

//             if ($harga <= 0) {
//                 throw new Exception('Harga jamaah belum ditentukan.');
//             }

//             // 🧮 Total komitmen (pending + valid)
//             $totalCommitted = Payments::where('jamaah_id', $jamaah->id)
//                 ->whereIn('status', [
//                     Payments::STATUS_PENDING,
//                     Payments::STATUS_VALID,
//                 ])
//                 ->sum('jumlah');

//             $sisa = $harga - $totalCommitted;

//             if ($amount <= 0 || $amount > $sisa) {
//                 throw new Exception('Nominal melebihi sisa pembayaran.');
//             }

//             // 💾 SIMPAN PAYMENT (PENDING)
//             $payment = Payments::create([
//                 'jamaah_id'     => $jamaah->id,
//                 'invoice_id'    => null,
//                 'metode'        => Payments::METODE_TRANSFER,
//                 'tanggal_bayar' => now(),
//                 'jumlah'        => $amount,
//                 'keterangan'    => "Input {$type} oleh agent",
//                 'status'        => Payments::STATUS_PENDING,
//                 'created_by'    => $agentId,
//             ]);

//             // 🧾 LOG INPUT AGENT
//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => PaymentLogs::ACTION_INPUT_AGENT,
//                 'context'    => PaymentLogs::CONTEXT_AGENT,
//                 'meta'       => [
//                     'type'   => $type,
//                     'amount' => $amount,
//                 ],
//                 'created_by' => $agentId,
//                 'created_at' => now(),
//             ]);

//             return $payment;
//         });
//     }
// }

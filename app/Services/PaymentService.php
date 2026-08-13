<?php

namespace App\Services;

use App\Models\Payments;
use App\Models\Invoices;
use App\Models\Jamaah;
use App\Models\Agent;
use App\Models\PaymentLogs;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class PaymentService
{
    /* ============================================================
     | INPUT PAYMENT (SEMUA ROLE)
     | - TIDAK BUAT INVOICE
     | - STATUS = pending
     ============================================================ */
    
    public function input(array $payload, int $userId): Payments
    {
        return DB::transaction(function () use ($payload, $userId) {

            $jamaah = Jamaah::lockForUpdate()
                ->findOrFail($payload['jamaah_id']);

            // 🚫 GUARD: TABUNGAN TIDAK BOLEH PAYMENT
            if ($jamaah->tipe_jamaah === 'tabungan') {
                throw new Exception(
                    'Jamaah dengan tipe TABUNGAN tidak boleh melakukan pembayaran. Gunakan Top Up Tabungan.'
                );
            }

            if ($jamaah->harga_akhir <= 0) {
                throw new Exception('Harga jamaah belum ditentukan.');
            }

            $buktiPath = null;
            if (!empty($payload['bukti_transfer'])) {
                $buktiPath = $payload['bukti_transfer']->store('payments', 'public');
            }

            $payment = Payments::create([
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

            PaymentLogs::create([
                'payment_id' => $payment->id,
                'action'     => PaymentLogs::ACTION_INPUT_AGENT,
                'context'    => PaymentLogs::CONTEXT_AGENT,
                'meta'       => [
                    'amount' => $payment->jumlah,
                    'method' => $payment->metode,
                ],
                'created_by' => $userId,
            ]);
            return $payment;
        });
    }

    
    /* ============================================================
    | APPROVE PAYMENT (KEUANGAN)
    | - STATUS -> VALID
    | - RECALCULATE INVOICE
    | - GENERATE KOMISI (PARTIAL PAYMENT)
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

            // 🚫 GUARD: TABUNGAN TIDAK BOLEH APPROVE PAYMENT
            if ($jamaah->tipe_jamaah === 'tabungan') {
                throw new Exception(
                    'Pembayaran untuk jamaah tipe TABUNGAN tidak dapat diproses. Gunakan Top Up Tabungan.'
                );
            }

            // 🔎 Cari invoice aktif
            $invoice = Invoices::where('jamaah_id', $jamaah->id)
                ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
                ->lockForUpdate()
                ->first();

            // 🧾 Buat invoice jika belum ada
            if (!$invoice) {
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
                    'status'         => 'BELUM_LUNAS',
                ]);
            }

            // ❌ Guard overpayment
            if ($payment->jumlah > $invoice->sisa_tagihan) {
                throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
            }

            // ✅ Approve payment
            $payment->update([
                'invoice_id'  => $invoice->id,
                'status'      => Payments::STATUS_VALID,
                'approved_by' => $adminId,
                'approved_at' => now(),
            ]);

            // 🔁 Recalculate invoice
            $this->recalculateInvoice($invoice);

            // 🧾 Log approve
            PaymentLogs::create([
                'payment_id' => $payment->id,
                'action'     => 'APPROVE',
                'meta'       => json_encode([
                    'invoice' => $invoice->nomor_invoice,
                    'amount'  => $payment->jumlah,
                ]),
                'created_by' => $adminId,
            ]);

            // 💸 KOMISI — SETELAH COMMIT
            DB::afterCommit(function () use ($jamaah, $payment) {
                app(\App\Services\Komisi\KomisiService::class)
                    ->generateFromPayment($jamaah, $payment);
            });

            return $payment;
        });
    }


    /* ============================================================
     | REJECT PAYMENT
     ============================================================ */

    public function reject(Payments $payment, int $adminId, string $reason): void
    {
        DB::transaction(function () use ($payment, $adminId, $reason) {

            $payment = Payments::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== Payments::STATUS_PENDING) {
                throw new Exception('Hanya payment pending yang bisa ditolak.');
            }

            // 🔍 Ambil jamaah (tanpa global scope) — READ ONLY
            $jamaah = Jamaah::withoutGlobalScopes()
                ->find($payment->jamaah_id);

            // ❗ Optional audit note
            if ($jamaah && $jamaah->tipe_jamaah === 'tabungan') {
                // tidak di-throw, hanya informasi audit
                // bisa juga dicatat di log
            }

            $payment->update([
                'status'       => Payments::STATUS_REJECTED,
                'rejected_by'  => $adminId,
                'rejected_at'  => now(),
            ]);

            PaymentLogs::create([
                'payment_id' => $payment->id,
                'action'     => PaymentLogs::ACTION_REJECT,
                'meta'       => json_encode([
                    'reason' => $reason,
                    'tipe_jamaah' => $jamaah?->tipe_jamaah,
                ]),
                'created_by' => $adminId,
            ]);
        });
    }

    /* ============================================================
     | RECALCULATE INVOICE
     ============================================================ */
    private function recalculateInvoice(Invoices $invoice): void
    {
        $totalPaid = Payments::where('invoice_id', $invoice->id)
            ->where('status', 'valid')
            ->where('is_deleted', 0)
            ->sum('jumlah');

        $sisa = max(0, $invoice->total_tagihan - $totalPaid);

        $invoice->update([
            'total_terbayar' => $totalPaid,
            'sisa_tagihan'   => $sisa,
            'status'         => $sisa === 0
                ? 'lunas'
                : ($totalPaid > 0 ? 'cicilan' : 'belum_lunas'),
        ]);
    }

    private function generateInvoiceNumber(): string
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

    /* ============================================================
     | FORMAT INPUT PAYMENT DARI AGENT
    ============================================================ */
    public function inputFromAgent(
        int $jamaahId,
        int $amount,
        string $label,
        int $agentId,
        $buktiTransfer = null
    ): Payments {
        return DB::transaction(function () use (
            $jamaahId,
            $amount,
            $label,
            $agentId,
            $buktiTransfer
        ) {

            $jamaah = Jamaah::withoutGlobalScopes()
                ->lockForUpdate()
                ->findOrFail($jamaahId);

            abort_if(
                $jamaah->agent_id !== $agentId,
                403,
                'Jamaah bukan milik agent'
            );

            // 🚫 GUARD: TABUNGAN TIDAK BOLEH PAYMENT
            if ($jamaah->tipe_jamaah === 'tabungan') {
                throw new Exception(
                    'Jamaah dengan tipe TABUNGAN tidak boleh melakukan pembayaran. Gunakan Top Up Tabungan.'
                );
            }

            $agent = Agent::withoutGlobalScopes()->findOrFail($agentId);

            if ($jamaah->harga_akhir <= 0) {
                throw new Exception('Harga jamaah belum ditentukan.');
            }

            $total = Payments::where('jamaah_id', $jamaah->id)
                ->whereIn('status', [
                    Payments::STATUS_PENDING,
                    Payments::STATUS_VALID,
                ])
                ->sum('jumlah');

            if ($amount <= 0 || $amount > ($jamaah->harga_akhir - $total)) {
                throw new Exception('Nominal melebihi sisa pembayaran.');
            }

            $buktiPath = null;
            if ($buktiTransfer) {
                $buktiPath = $buktiTransfer->store('payments', 'public');
            }

            $keterangan = sprintf(
                '%s oleh Agent %s (%s)',
                trim($label) !== '' ? $label : 'Pembayaran',
                $agent->nama,
                $agent->kode_agent
            );

            $payment = Payments::create([
                'jamaah_id'      => $jamaah->id,
                'invoice_id'     => null,
                'metode'         => Payments::METODE_TRANSFER,
                'tanggal_bayar'  => now()->startOfDay(),
                'jumlah'         => $amount,
                'keterangan'     => $keterangan,
                'bukti_transfer' => $buktiPath,
                'status'         => Payments::STATUS_PENDING,
                'created_by'     => $agentId,
            ]);

            PaymentLogs::create([
                'payment_id' => $payment->id,
                'action'     => PaymentLogs::ACTION_INPUT_AGENT,
                'context'    => PaymentLogs::CONTEXT_AGENT,
                'meta'       => [
                    'agent_id'   => $agent->id,
                    'agent_nama' => $agent->nama,
                    'amount'     => $amount,
                    'label'      => $label,
                ],
                'created_by' => $agentId,
            ]);

            return $payment;
        });
    }

    private function buildAgentKeterangan(string $type, Agent $agent): string
    {
        return sprintf(
            'Input %s oleh Agent: %s (%s)',
            ucfirst($type),
            $agent->nama,
            $agent->kode_agent
        );
    }

    private function guardNotTabungan(Jamaah $jamaah): void
    {
        if ($jamaah->tipe_jamaah === 'tabungan') {
            throw new Exception(
                'Jamaah dengan tipe TABUNGAN tidak boleh melakukan payment. Gunakan Top Up Tabungan.'
            );
        }
    }

}

    //  public function input(array $payload, int $userId): Payments
    // {
    //     return DB::transaction(function () use ($payload, $userId) {

    //         $jamaah = Jamaah::lockForUpdate()->findOrFail($payload['jamaah_id']);

    //         if ($jamaah->harga_akhir <= 0) {
    //             throw new Exception('Harga jamaah belum ditentukan.');
    //         }

    //         $buktiPath = null;
    //         if (!empty($payload['bukti_transfer'])) {
    //             $buktiPath = $payload['bukti_transfer']->store('payments', 'public');
    //         }

    //         $payment = Payments::create([
    //             'jamaah_id'      => $jamaah->id,
    //             'invoice_id'     => null,
    //             'metode'         => $payload['metode'],
    //             'tanggal_bayar'  => Carbon::parse($payload['tanggal_bayar'])->startOfDay(),
    //             'jumlah'         => (int) $payload['jumlah'],
    //             'keterangan'     => $payload['keterangan'] ?? 'Input pembayaran',
    //             'bukti_transfer' => $buktiPath,
    //             'status'         => 'PENDING',
    //             'created_by'     => $userId,
    //         ]);

    //         PaymentLogs::create([
    //             'payment_id' => $payment->id,
    //             'action'     => 'INPUT',
    //             'meta'       => json_encode(['amount' => $payment->jumlah]),
    //             'created_by' => $userId,
    //         ]);

    //         return $payment;
    //     });
    // }

    //     public function input(array $payload, int $userId): Payments
    // {
    //     return DB::transaction(function () use ($payload, $userId) {

    //         $jamaah = Jamaah::lockForUpdate()
    //             ->findOrFail($payload['jamaah_id']);

    //         if ($jamaah->harga_akhir <= 0) {
    //             throw new Exception('Harga jamaah belum ditentukan.');
    //         }

    //         $buktiPath = null;
    //         if (!empty($payload['bukti_transfer'])) {
    //             $buktiPath = $payload['bukti_transfer']->store('payments', 'public');
    //         }

    //         $payment = Payments::create([
    //             'jamaah_id'      => $jamaah->id,
    //             'invoice_id'     => null,
    //             'metode'         => $payload['metode'],
    //             'tanggal_bayar'  => Carbon::parse($payload['tanggal_bayar'])->startOfDay(),
    //             'jumlah'         => (int) $payload['jumlah'],
    //             'keterangan'     => $payload['keterangan'] ?? 'Input pembayaran',
    //             'bukti_transfer' => $buktiPath,
    //             'status'         => Payments::STATUS_PENDING,
    //             'created_by'     => $userId,
    //         ]);

    //         PaymentLogs::create([
    //             'payment_id' => $payment->id,
    //             'action'     => PaymentLogs::ACTION_INPUT,
    //             'meta'       => [
    //                 'amount' => $payment->jumlah,
    //             ],
    //             'created_by' => $userId,
    //         ]);

    //         return $payment;
    //     });
    // }

    //     public function approve(Payments $payment, int $adminId): Payments
    // {
    //     return DB::transaction(function () use ($payment, $adminId) {

    //         // 🔒 Lock payment
    //         $payment = Payments::lockForUpdate()->findOrFail($payment->id);

    //         if ($payment->status !== Payments::STATUS_PENDING) {
    //             throw new Exception('Pembayaran sudah diproses.');
    //         }

    //         // 🔒 Lock jamaah (tanpa global scope)
    //         $jamaah = Jamaah::withoutGlobalScopes()
    //             ->lockForUpdate()
    //             ->findOrFail($payment->jamaah_id);

    //         // 🔎 Cari invoice aktif
    //         $invoice = Invoices::where('jamaah_id', $jamaah->id)
    //             ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
    //             ->lockForUpdate()
    //             ->first();

    //         // 🧾 Buat invoice jika belum ada
    //         if (!$invoice) {
    //             $total = (int) $jamaah->harga_akhir;

    //             if ($total <= 0) {
    //                 throw new Exception('Harga jamaah belum valid.');
    //             }

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

    //         // ❌ Guard overpayment
    //         if ($payment->jumlah > $invoice->sisa_tagihan) {
    //             throw new Exception('Jumlah pembayaran melebihi sisa tagihan.');
    //         }

    //         // ✅ Approve payment
    //         $payment->update([
    //             'invoice_id'  => $invoice->id,
    //             'status'      => Payments::STATUS_VALID,
    //             'approved_by' => $adminId,
    //             'approved_at' => now(),
    //         ]);

    //         // 🔁 Recalculate invoice
    //         $this->recalculateInvoice($invoice);

    //         // 🧾 Log approve
    //         PaymentLogs::create([
    //             'payment_id' => $payment->id,
    //             'action'     => 'APPROVE',
    //             'meta'       => json_encode([
    //                 'invoice' => $invoice->nomor_invoice,
    //                 'amount'  => $payment->jumlah,
    //             ]),
    //             'created_by' => $adminId,
    //         ]);

    //         // 💸 KOMISI — SETELAH COMMIT
    //         DB::afterCommit(function () use ($jamaah, $payment) {
    //             app(\App\Services\Komisi\KomisiService::class)
    //                 ->generateFromPayment($jamaah, $payment);
    //         });

    //         return $payment;
    //     });
    // }

    //     public function reject(Payments $payment, int $adminId, string $reason): void
    // {
    //     DB::transaction(function () use ($payment, $adminId, $reason) {

    //         $payment = Payments::lockForUpdate()->findOrFail($payment->id);

    //         if ($payment->status !== 'PENDING') {
    //             throw new Exception('Hanya payment pending yang bisa ditolak.');
    //         }

    //         $payment->update([
    //             'status'       => 'REJECTED',
    //             'rejected_by'  => $adminId,
    //             'rejected_at'  => now(),
    //         ]);

    //         PaymentLogs::create([
    //             'payment_id' => $payment->id,
    //             'action'     => 'REJECT',
    //             'meta'       => json_encode(['reason' => $reason]),
    //             'created_by' => $adminId,
    //         ]);
    //     });
    // }

//         public function inputFromAgent(
//     int $jamaahId,
//     int $amount,
//     string $label,
//     int $agentId,
//     $buktiTransfer = null
// ): Payments {
//     return DB::transaction(function () use (
//         $jamaahId,
//         $amount,
//         $label,
//         $agentId,
//         $buktiTransfer
//     ) {

//         $jamaah = Jamaah::withoutGlobalScopes()
//             ->lockForUpdate()
//             ->findOrFail($jamaahId);

//         abort_if(
//             $jamaah->agent_id !== $agentId,
//             403,
//             'Jamaah bukan milik agent'
//         );

//         $agent = Agent::withoutGlobalScopes()->findOrFail($agentId);

//         if ($jamaah->harga_akhir <= 0) {
//             throw new Exception('Harga jamaah belum ditentukan.');
//         }

//         $total = Payments::where('jamaah_id', $jamaah->id)
//             ->whereIn('status', [
//                 Payments::STATUS_PENDING,
//                 Payments::STATUS_VALID,
//             ])
//             ->sum('jumlah');

//         if ($amount <= 0 || $amount > ($jamaah->harga_akhir - $total)) {
//             throw new Exception('Nominal melebihi sisa pembayaran.');
//         }

//         $buktiPath = null;
//         if ($buktiTransfer) {
//             $buktiPath = $buktiTransfer->store('payments', 'public');
//         }

//         // ✅ SATU FORMAT RESMI
//         $keterangan = sprintf(
//             '%s oleh Agent %s (%s)',
//             trim($label) !== '' ? $label : 'Pembayaran',
//             $agent->nama,
//             $agent->kode_agent
//         );

//         $payment = Payments::create([
//             'jamaah_id'      => $jamaah->id,
//             'invoice_id'     => null,
//             'metode'         => Payments::METODE_TRANSFER,
//             'tanggal_bayar'  => now()->startOfDay(),
//             'jumlah'         => $amount,
//             'keterangan'     => $keterangan,
//             'bukti_transfer' => $buktiPath,
//             'status'         => Payments::STATUS_PENDING,
//             'created_by'     => $agentId,
//         ]);

//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_INPUT_AGENT,
//             'context'    => PaymentLogs::CONTEXT_AGENT,
//             'meta'       => [
//                 'agent_id'   => $agent->id,
//                 'agent_nama' => $agent->nama,
//                 'amount'     => $amount,
//                 'label'      => $label,
//             ],
//             'created_by' => $agentId,
//         ]);

//         return $payment;
//     });
// }

<?php

namespace App\Services\Ticketing;

use App\Models\TicketRefund;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketRefundApprovalService
{
    public function approve(TicketRefund $refund, int $userId): void
    {
        DB::transaction(function () use ($refund, $userId) {

            if ($refund->approval_status !== 'PENDING') {
                throw new Exception('Refund sudah diproses.');
            }

            // 🔒 LOCK INVOICE UNTUK SAFETY
            $refund->invoice()->lockForUpdate()->firstOrFail();

            // ✅ HANYA UPDATE STATUS REFUND
            $refund->approval_status = 'APPROVED';
            $refund->status          = 'REFUNDED';
            $refund->approved_by     = $userId;
            $refund->approved_at     = now();

            // 🔥 WAJIB save() → TRIGGER OBSERVER
            $refund->save();
        });
    }

    public function reject(TicketRefund $refund, int $userId): void
    {
        DB::transaction(function () use ($refund, $userId) {

            if ($refund->approval_status !== 'PENDING') {
                throw new Exception('Refund sudah diproses.');
            }

            $refund->approval_status = 'REJECTED';
            $refund->status          = 'REJECTED';
            $refund->approved_by     = $userId;
            $refund->approved_at     = now();

            $refund->save();
        });
    }
}

// namespace App\Services\Ticketing;

// use App\Models\TicketRefund;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class TicketRefundApprovalService
// {
//     public function approve(TicketRefund $refund, int $userId): void
//     {
//         DB::transaction(function () use ($refund, $userId) {

//             if ($refund->approval_status !== 'PENDING') {
//                 throw new Exception('Refund sudah diproses.');
//             }

//             $invoice = $refund->invoice()->lockForUpdate()->first();

//             if (!$invoice) {
//                 throw new Exception('Invoice tidak ditemukan.');
//             }

//             // 🔢 HITUNG TOTAL REFUND YANG SUDAH APPROVED
//             $totalRefundApproved = $invoice->refunds()
//                 ->where('approval_status', 'APPROVED')
//                 ->sum('amount');

//             $remainingPaid = $invoice->paid_amount - $totalRefundApproved;

//             if ($refund->amount > $remainingPaid) {
//                 throw new Exception('Jumlah refund melebihi pembayaran yang tersedia.');
//             }

//             // 1️⃣ UPDATE REFUND
//             $refund->update([
//                 'approval_status' => 'APPROVED',
//                 'status'          => 'REFUNDED',
//                 'approved_by'     => $userId,
//                 'approved_at'     => now(),
//             ]);

//             // 2️⃣ RECALC INVOICE
//             $newPaid = $remainingPaid - $refund->amount;

//             $invoice->paid_amount = max(0, $newPaid);

//             if ($invoice->paid_amount <= 0) {
//                 $invoice->status = 'UNPAID';
//             } elseif ($invoice->paid_amount < $invoice->total_amount) {
//                 $invoice->status = 'PARTIAL';
//             } else {
//                 $invoice->status = 'PAID';
//             }

//             $invoice->save();
//         });
//     }

//     public function reject(TicketRefund $refund, int $userId): void
//     {
//         if ($refund->approval_status !== 'PENDING') {
//             throw new Exception('Refund sudah diproses.');
//         }

//         $refund->update([
//             'approval_status' => 'REJECTED',
//             'status'          => 'REJECTED',
//             'approved_by'     => $userId,
//             'approved_at'     => now(),
//         ]);
//     }
// }


// namespace App\Services\Ticketing;

// use App\Models\TicketRefund;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class TicketRefundApprovalService
// {
//     public function approve(TicketRefund $refund, int $userId): void
//     {
//         DB::transaction(function () use ($refund, $userId) {

//             // 🔒 VALIDASI STATUS
//             if ($refund->approval_status !== 'PENDING') {
//                 throw new Exception('Refund sudah diproses.');
//             }

//             $invoice = $refund->invoice;

//             if (!$invoice) {
//                 throw new Exception('Invoice tidak ditemukan.');
//             }

//             // 🔒 VALIDASI KEUANGAN
//             if ($refund->amount > $invoice->paid_amount) {
//                 throw new Exception('Jumlah refund melebihi pembayaran.');
//             }

//             // 1️⃣ UPDATE REFUND
//             $refund->update([
//                 'approval_status' => 'APPROVED',
//                 'status'          => 'REFUNDED',
//                 'approved_by'     => $userId,
//                 'approved_at'     => now(),
//             ]);

//             // 2️⃣ UPDATE INVOICE KEUANGAN
//             $invoice->paid_amount -= $refund->amount;

//             if ($invoice->paid_amount <= 0) {
//                 $invoice->paid_amount = 0;
//                 $invoice->status = 'UNPAID';
//             }
//             elseif ($invoice->paid_amount < $invoice->total_amount) {
//                 $invoice->status = 'PARTIAL';
//             }
//             else {
//                 $invoice->status = 'PAID';
//             }

//             $invoice->save();

//             // ⚠️ PNR TIDAK DIUBAH (tetap ISSUED)
//         });
//     }

//     public function reject(TicketRefund $refund, int $userId): void
//     {
//         if ($refund->approval_status !== 'PENDING') {
//             throw new Exception('Refund sudah diproses.');
//         }

//         $refund->update([
//             'approval_status' => 'REJECTED',
//             'status'          => 'REJECTED',
//             'approved_by'     => $userId,
//             'approved_at'     => now(),
//         ]);
//     }
// }

// namespace App\Services\Ticketing;

// use App\Models\TicketRefund;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;

// class TicketRefundApprovalService
// {
//     /**
//      * APPROVE REFUND (CHECKER)
//      */
//     public function approve(int $refundId): void
//     {
//         DB::transaction(function () use ($refundId) {

//             $refund = TicketRefund::lockForUpdate()
//                 ->with(['invoice.pnr'])
//                 ->findOrFail($refundId);

//             if ($refund->approval_status !== 'PENDING') {
//                 throw new \Exception('Refund sudah diproses');
//             }

//             // eksekusi refund finansial + PNR
//             app(TicketRefundService::class)
//                 ->executeApprovedRefund($refund);

//             $refund->update([
//                 'approval_status' => 'APPROVED',
//                 'approved_by'     => Auth::id(),
//                 'approved_at'     => now(),
//             ]);
//         });
//     }

//     /**
//      * REJECT REFUND
//      */
//     public function reject(int $refundId, ?string $reason = null): void
//     {
//         DB::transaction(function () use ($refundId, $reason) {

//             $refund = TicketRefund::lockForUpdate()
//                 ->findOrFail($refundId);

//             if ($refund->approval_status !== 'PENDING') {
//                 throw new \Exception('Refund sudah diproses');
//             }

//             $refund->update([
//                 'approval_status' => 'REJECTED',
//                 'approved_by'     => Auth::id(),
//                 'approved_at'     => now(),
//                 'reason'          => $reason,
//             ]);
//         });
//     }
// }

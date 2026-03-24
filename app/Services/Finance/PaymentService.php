<?php

namespace App\Services\Finance;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected CodeGeneratorService $codeService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | NORMALIZE & GUARD INPUT
            |--------------------------------------------------------------------------
            */

            $bookingId = data_get($data, 'booking_id')
                ?? throw new RuntimeException('Booking tidak ditemukan.');

            $method = strtolower(trim((string) data_get($data, 'method', '')));
            if (!$method) {
                throw new RuntimeException('Metode pembayaran tidak valid / tidak terbaca.');
            }

            $type = data_get($data, 'type');

            $amount = (float) data_get($data, 'amount', 0);
            $fee    = (float) data_get($data, 'fee_amount', 0);

            /*
            |--------------------------------------------------------------------------
            | LOCK BOOKING
            |--------------------------------------------------------------------------
            */

            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            /*
            |--------------------------------------------------------------------------
            | AUTH & ACCESS
            |--------------------------------------------------------------------------
            */

            $user = auth()->user()
                ?? throw new RuntimeException('User tidak terautentikasi.');

            if (!$booking->isOwnedBy($user)) {
                throw new RuntimeException('Tidak memiliki akses booking.');
            }

            /*
            |--------------------------------------------------------------------------
            | BOOKING GUARD
            |--------------------------------------------------------------------------
            */

            if ($booking->isExpired()) {
                throw new RuntimeException('Booking sudah expired.');
            }

            if (in_array($booking->status, ['cancelled', 'confirmed'], true)) {
                throw new RuntimeException('Booking tidak bisa dibayar.');
            }

            if ($booking->isFullyPaid()) {
                throw new RuntimeException('Booking sudah lunas.');
            }

            /*
            |--------------------------------------------------------------------------
            | JAMAAH VALIDATION
            |--------------------------------------------------------------------------
            */

            $jamaahId = data_get($data, 'jamaah_id');
            $this->ensureValidBookingJamaah($booking, $jamaahId);

            /*
            |--------------------------------------------------------------------------
            | AMOUNT VALIDATION
            |--------------------------------------------------------------------------
            */

            $this->validateAmounts($amount, $fee);

            $paid = $this->sumApprovedPayments($booking);
            $remaining = max(0, (float) $booking->total_amount - $paid);

            if ($amount > $remaining) {
                throw new RuntimeException('Pembayaran melebihi sisa tagihan.');
            }

            /*
            |--------------------------------------------------------------------------
            | TYPE RESOLVER
            |--------------------------------------------------------------------------
            */

            $type = $this->resolvePaymentTypeForCreate(
                booking: $booking,
                amount: $amount,
                remaining: $remaining,
                requestedType: $type
            );

            /*
            |--------------------------------------------------------------------------
            | HANDLE FILE UPLOAD (IMPORTANT FIX)
            |--------------------------------------------------------------------------
            */

            $proofFile = null;

            if (isset($data['proof_file']) && $data['proof_file'] instanceof \Illuminate\Http\UploadedFile) {
                $proofFile = $data['proof_file']->store('payments', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE CODE
            |--------------------------------------------------------------------------
            */

            $paymentCode = $this->codeService->generate(
                prefix: 'PAY',
                entity: 'payment_' . now()->format('Ymd'),
                pad: 4,
                yearly: false
            );

            /*
            |--------------------------------------------------------------------------
            | CREATE PAYMENT
            |--------------------------------------------------------------------------
            */

            $payment = Payment::create([
                'booking_id'          => $booking->id,
                'jamaah_id'           => $jamaahId,

                'paket_departure_id'  => $booking->paket_departure_id,
                'branch_id'           => $booking->branch_id,

                'payment_code'        => $paymentCode,
                'reference_number'    => data_get($data, 'reference_number'),
                'invoice_number'      => $booking->invoice_number,

                'type'                => $type,
                'method'              => $method,
                'channel'             => data_get($data, 'channel', 'website'),

                'amount'              => $amount,
                'fee_amount'          => $fee,
                'net_amount'          => $amount - $fee,

                'status'              => 'pending',
                'paid_at'             => data_get($data, 'paid_at', now()),

                'note'                => data_get($data, 'note'),
                'proof_file'          => $proofFile,

                'created_by'          => $user->id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */

            $this->logPayment(
                payment: $payment,
                action: 'created',
                old: null,
                new: $payment->getAttributes(),
                desc: 'Payment created'
            );

            return $payment->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function update(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {

            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            // 🔒 STATUS GUARD
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment hanya bisa diubah saat status pending.');
            }

            $booking = Booking::lockForUpdate()->findOrFail($payment->booking_id);

            // 🔒 BOOKING GUARD
            if ($booking->isExpired()) {
                throw new RuntimeException('Booking sudah expired.');
            }

            if (in_array($booking->status, ['cancelled', 'confirmed'], true)) {
                throw new RuntimeException('Booking tidak bisa diubah.');
            }

            $oldData = $payment->getAttributes();

            // 💰 AMOUNT
            $amount = isset($data['amount'])
                ? (float) $data['amount']
                : (float) $payment->amount;

            $fee = isset($data['fee_amount'])
                ? (float) $data['fee_amount']
                : (float) $payment->fee_amount;

            $this->validateAmounts($amount, $fee);

            // 👤 JAMAAH
            $jamaahId = $data['jamaah_id'] ?? $payment->jamaah_id;
            $this->ensureValidBookingJamaah($booking, $jamaahId);

            // 💳 REMAINING
            $otherPaid = (float) $booking->payments()
                ->where('status', 'paid')
                ->where('id', '!=', $payment->id)
                ->sum('amount');

            $remaining = max(0, (float) $booking->total_amount - $otherPaid);

            if ($amount > $remaining) {
                throw new RuntimeException('Pembayaran melebihi sisa tagihan.');
            }

            // 🔄 TYPE RESOLVER
            $type = $this->resolvePaymentTypeForUpdate(
                booking: $booking,
                currentPayment: $payment,
                amount: $amount,
                remainingAllowed: $remaining,
                requestedType: $data['type'] ?? null
            );

            // 🧾 BUILD UPDATE DATA
            $updateData = [
                'jamaah_id'       => $jamaahId,
                'type'            => $type,
                'method'          => $data['method'] ?? $payment->method,
                'channel'         => $data['channel'] ?? $payment->channel,
                'reference_number'=> $data['reference_number'] ?? $payment->reference_number,
                'amount'          => $amount,
                'fee_amount'      => $fee,
                'net_amount'      => $amount - $fee,
                'paid_at'         => $data['paid_at'] ?? $payment->paid_at,
                'note'            => $data['note'] ?? $payment->note,
            ];

            // 📎 FILE HANDLING (SAFE)
            if (array_key_exists('proof_file', $data)) {

                if ($data['proof_file'] === $payment->proof_file) {
                    // skip (tidak berubah)
                } elseif (!empty($data['proof_file'])) {

                    if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
                        Storage::disk('public')->delete($payment->proof_file);
                    }

                    $updateData['proof_file'] = $data['proof_file'];

                } else {

                    if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
                        Storage::disk('public')->delete($payment->proof_file);
                    }

                    $updateData['proof_file'] = null;
                }
            }

            // 🚫 SKIP JIKA TIDAK ADA PERUBAHAN (OPTIONAL OPTIMIZATION)
            if (empty(array_diff_assoc($updateData, $payment->only(array_keys($updateData))))) {
                return $payment;
            }

            // 💾 UPDATE
            $payment->update($updateData);

            // 📝 LOG
            $this->logPayment(
                payment: $payment,
                action: 'updated',
                old: $oldData,
                new: $payment->fresh()->getAttributes(),
                desc: 'Payment updated'
            );

            return $payment->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function approve(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {

            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            // ✅ IDEMPOTENT (PENTING BANGET)
            if ($payment->status === 'paid') {
                return $payment;
            }

            // ❌ INVALID STATE
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak bisa diproses.');
            }

            $booking = Booking::lockForUpdate()->findOrFail($payment->booking_id);

            // 🔒 BOOKING GUARD
            if ($booking->isExpired()) {
                throw new RuntimeException('Booking sudah expired.');
            }

            if (in_array($booking->status, ['cancelled'], true)) {
                throw new RuntimeException('Booking sudah dibatalkan.');
            }

            // 👤 VALIDASI JAMAAH
            $this->ensureValidBookingJamaah($booking, $payment->jamaah_id);

            // 💰 CEK SISA TAGIHAN
            $paidBefore = $this->sumApprovedPayments($booking);
            $remaining = max(0, (float) $booking->total_amount - $paidBefore);

            if ((float) $payment->amount > $remaining) {
                throw new RuntimeException('Approval payment melebihi sisa tagihan booking.');
            }

            // 🧾 GENERATE RECEIPT (ONCE)
            $receiptNumber = $payment->receipt_number ?: $this->codeService->generate(
                prefix: 'RCPT',
                entity: 'receipt_' . now()->format('Ym'),
                pad: 5,
                yearly: true
            );

            $oldData = $payment->getAttributes();

            // 💾 UPDATE PAYMENT
            $payment->update([
                'receipt_number' => $receiptNumber,
                'status'         => 'paid',
                'approved_by'    => auth()->id(),
                'approved_at'    => now(),
            ]);

            // 📝 LOG
            $this->logPayment(
                payment: $payment,
                action: 'approved',
                old: $oldData,
                new: $payment->fresh()->getAttributes(),
                desc: 'Payment approved'
            );

            // 🔄 SYNC BOOKING
            $this->syncBookingPayment($booking);

            return $payment->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PAYMENT
    |--------------------------------------------------------------------------
    */

    public function reject(Payment $payment, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {

            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            // ❌ HARUS STRICT (TIDAK BOLEH SILENT)
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak bisa ditolak.');
            }

            $oldData = $payment->getAttributes();

            // 📝 NOTE MERGE (RAPI)
            $note = $payment->note;

            if ($reason) {
                $note = trim(
                    ($note ? $note . PHP_EOL : '') .
                    'Rejected: ' . $reason
                );
            }

            // 💾 UPDATE
            $payment->update([
                'status'       => 'failed',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
                'note'         => $note,
            ]);

            // 📝 LOG
            $this->logPayment(
                payment: $payment,
                action: 'rejected',
                old: $oldData,
                new: $payment->fresh()->getAttributes(),
                desc: 'Payment rejected'
            );

            return $payment->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL PAYMENT
    |--------------------------------------------------------------------------
    */

    public function cancel(Payment $payment, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {

            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            // ❌ HARUS STRICT
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak bisa dibatalkan.');
            }

            $oldData = $payment->getAttributes();

            // 📝 NOTE MERGE
            $note = $payment->note;

            if ($reason) {
                $note = trim(
                    ($note ? $note . PHP_EOL : '') .
                    'Cancelled: ' . $reason
                );
            }

            // 💾 UPDATE
            $payment->update([
                'status'       => 'cancelled',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
                'note'         => $note,
            ]);

            // 📝 LOG
            $this->logPayment(
                payment: $payment,
                action: 'cancelled',
                old: $oldData,
                new: $payment->fresh()->getAttributes(),
                desc: 'Payment cancelled'
            );

            return $payment->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function delete(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if (!in_array($payment->status, ['pending', 'failed', 'cancelled'], true)) {
                throw new RuntimeException('Payment dengan status ini tidak dapat dihapus.');
            }

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($payment->booking_id);

            $this->logPayment(
                payment: $payment,
                action: 'deleted',
                old: $payment->getAttributes(),
                new: null,
                desc: 'Payment deleted'
            );

            if (
                $payment->proof_file &&
                Storage::disk('public')->exists($payment->proof_file)
            ) {
                Storage::disk('public')->delete($payment->proof_file);
            }

            $payment->delete();

            $this->syncBookingPayment($booking);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC BOOKING PAYMENT
    |--------------------------------------------------------------------------
    */

    protected function syncBookingPayment(Booking $booking): void
    {
        $booking->refresh();

        $totalPaid = (float) $booking->payments()
            ->where('status', 'paid')
            ->sum('amount');

        $total = (float) $booking->total_amount;

        if (in_array($booking->status, ['cancelled', 'expired', 'confirmed'], true)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 🔥 BASE UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $updateData = [
            'paid_amount' => $totalPaid,
        ];

        /*
        |--------------------------------------------------------------------------
        | 🔥 MATIKAN EXPIRY JIKA SUDAH ADA PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if ($totalPaid > 0) {
            $updateData['expired_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS LOGIC
        |--------------------------------------------------------------------------
        */

        if ($totalPaid <= 0) {
            $updateData['status'] = 'waiting_payment';
            $booking->update($updateData);
            return;
        }

        if ($totalPaid < $total) {
            $updateData['status'] = 'partial_paid';
            $booking->update($updateData);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | LUNAS
        |--------------------------------------------------------------------------
        */

        $booking->update($updateData);

        app(\App\Services\Bookings\BookingWorkflowService::class)
            ->confirm($booking->fresh());
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function sumApprovedPayments(Booking $booking): float
    {
        return (float) $booking->payments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    protected function ensureValidBookingJamaah(Booking $booking, mixed $jamaahId): void
    {
        if (is_null($jamaahId) || $jamaahId === '') {
            return;
        }

        $exists = $booking->jamaahs()
            ->where('jamaahs.id', $jamaahId)
            ->whereNull('jamaahs.deleted_at')
            ->exists();

        if (!$exists) {
            throw new RuntimeException('Jamaah tidak terdaftar pada booking ini.');
        }
    }

    protected function validateAmounts(float $amount, float $fee): void
    {
        if ($amount <= 0) {
            throw new RuntimeException('Nominal pembayaran harus lebih dari 0.');
        }

        if ($fee < 0) {
            throw new RuntimeException('Biaya admin tidak boleh kurang dari 0.');
        }

        if ($fee > $amount) {
            throw new RuntimeException('Biaya admin tidak boleh melebihi jumlah pembayaran.');
        }
    }

    protected function resolvePaymentTypeForCreate(
        Booking $booking,
        float $amount,
        float $remaining,
        ?string $requestedType = null
    ): string {
        if (in_array($requestedType, ['add_on', 'upgrade', 'adjustment'], true)) {
            return $requestedType;
        }

        if (in_array($requestedType, ['dp', 'cicilan', 'pelunasan'], true)) {
            return $requestedType;
        }

        $hasApprovedPayment = $booking->payments()
            ->where('status', 'paid')
            ->exists();

        if (!$hasApprovedPayment) {
            return 'dp';
        }

        if ($amount >= $remaining) {
            return 'pelunasan';
        }

        return 'cicilan';
    }

    protected function resolvePaymentTypeForUpdate(
        Booking $booking,
        Payment $currentPayment,
        float $amount,
        float $remainingAllowed,
        ?string $requestedType = null
    ): string {
        if (in_array($requestedType, ['add_on', 'upgrade', 'adjustment'], true)) {
            return $requestedType;
        }

        if (in_array($requestedType, ['dp', 'cicilan', 'pelunasan'], true)) {
            return $requestedType;
        }

        $hasOtherApprovedPayment = $booking->payments()
            ->where('status', 'paid')
            ->where('id', '!=', $currentPayment->id)
            ->exists();

        if (!$hasOtherApprovedPayment) {
            return 'dp';
        }

        if ($amount >= $remainingAllowed) {
            return 'pelunasan';
        }

        return 'cicilan';
    }

    protected function logPayment(
        Payment $payment,
        string $action,
        $old = null,
        $new = null,
        ?string $desc = null
    ): void {
        PaymentLog::create([
            'payment_id' => $payment->id,
            'action' => $action,
            'old_data' => $old,
            'new_data' => $new,
            'description' => $desc,
            'created_by' => auth()->id(),
        ]);
    }
}
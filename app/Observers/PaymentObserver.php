<?php

namespace App\Observers;

use App\Models\Payments;
use App\Services\Payment\PaymentLogService;
use Illuminate\Support\Facades\Auth;

class PaymentObserver
{
    public function __construct(
        protected PaymentLogService $log
    ) {}

    /* =====================================================
     | CREATED
     ===================================================== */
    public function created(Payments $payment): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->log->systemInput($payment);
            return;
        }

        // SEMUA INPUT → ACTION = INPUT
        $this->log->input($payment, $user);
    }

    /* =====================================================
     | UPDATED
     ===================================================== */
    public function updated(Payments $payment): void
    {
        // safety: kalau tidak ada perubahan nyata
        if ($payment->getChanges() === []) {
            return;
        }

        $user = Auth::user();

        if ($payment->wasChanged('status')) {

            match ($payment->status) {
                Payments::STATUS_VALID    => $this->log->approve($payment, $user),
                Payments::STATUS_REJECTED => $this->log->reject($payment, $user),
                default => null,
            };

            return;
        }

        $this->log->update($payment, $user);
    }

    /* =====================================================
     | DELETED (REAL DELETE ONLY)
     ===================================================== */
    public function deleted(Payments $payment): void
    {
        $this->log->delete($payment, Auth::user());
    }
}

<?php

namespace App\Services\Payment;

use App\Models\PaymentLogs;
use App\Models\Payments;

class PaymentLogService
{
    /* =====================================================
     | INPUT PAYMENT
     ===================================================== */
    public function input(Payments $payment): void
    {
        $this->log(
            payment: $payment,
            action: PaymentLogs::ACTION_INPUT,
            meta: [
                'amount' => $payment->jumlah,
                'method' => $payment->metode,
            ]
        );
    }

    /* =====================================================
     | APPROVE PAYMENT
     ===================================================== */
    public function approve(Payments $payment): void
    {
        $this->log(
            payment: $payment,
            action: PaymentLogs::ACTION_APPROVE,
            meta: [
                'invoice_id' => $payment->invoice_id,
                'amount'     => $payment->jumlah,
            ],
            old: $payment->getOriginal(),
            new: $payment->getAttributes()
        );
    }

    /* =====================================================
     | REJECT PAYMENT
     ===================================================== */
    public function reject(Payments $payment): void
    {
        $this->log(
            payment: $payment,
            action: PaymentLogs::ACTION_REJECT,
            meta: [
                'reason' => $payment->reject_note,
            ],
            old: $payment->getOriginal(),
            new: $payment->getAttributes()
        );
    }

    /* =====================================================
     | UPDATE PAYMENT (NON STATUS)
     ===================================================== */
    public function update(Payments $payment): void
    {
        $this->log(
            payment: $payment,
            action: PaymentLogs::ACTION_UPDATE,
            old: $payment->getOriginal(),
            new: $payment->getChanges()
        );
    }

    /* =====================================================
     | DELETE PAYMENT
     ===================================================== */
    public function delete(Payments $payment): void
    {
        $this->log(
            payment: $payment,
            action: PaymentLogs::ACTION_DELETE,
            old: $payment->getOriginal()
        );
    }

    /* =====================================================
     | CORE LOGGER (SATU PINTU)
     ===================================================== */
    protected function log(
        Payments $payment,
        string $action,
        array $meta = [],
        array|null $old = null,
        array|null $new = null
    ): void {
        [$context, $actorName] = $this->resolveContextWithActor();

        PaymentLogs::create([
            'payment_id' => $payment->id,
            'action'     => $action,

            // 🔥 INI PENTING (BUKAN DI META)
            'context'    => $context,

            // 🧾 META = DETAIL AUDIT
            'meta'       => array_merge($meta, [
                'actor' => $actorName,
            ]),

            'old_value'  => $old,
            'new_value'  => $new,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    /* =====================================================
     | CONTEXT + ACTOR NAME (FINAL)
     ===================================================== */
    protected function resolveContextWithActor(): array
    {
        $user = auth()->user();

        // SYSTEM / CRON
        if (! $user) {
            return [
                PaymentLogs::CONTEXT_SYSTEM,
                'SYSTEM'
            ];
        }

        // AGENT
        if ($user->isAgent()) {
            return [
                PaymentLogs::CONTEXT_AGENT,
                optional($user->agent)->nama ?? $user->nama
            ];
        }

        // CABANG
        if ($user->isCabang()) {
            return [
                PaymentLogs::CONTEXT_CABANG,
                optional($user->branch)->nama_cabang ?? 'CABANG'
            ];
        }

        // KEUANGAN / SUPERADMIN
        return [
            PaymentLogs::CONTEXT_KEUANGAN,
            $user->nama
        ];
    }
}

// namespace App\Services\Payment;

// use App\Models\PaymentLogs;
// use App\Models\Payments;

// class PaymentLogService
// {
//     /* ==============================
//      | INPUT PAYMENT
//      ============================== */
//     public function input(Payments $payment): void
//     {
//         $this->log(
//             payment: $payment,
//             action: PaymentLogs::ACTION_INPUT,
//             meta: [
//                 'amount' => $payment->jumlah,
//                 'method' => $payment->metode,
//             ]
//         );
//     }

//     /* ==============================
//      | APPROVE PAYMENT
//      ============================== */
//     public function approve(Payments $payment): void
//     {
//         $this->log(
//             payment: $payment,
//             action: PaymentLogs::ACTION_APPROVE,
//             meta: [
//                 'invoice_id' => $payment->invoice_id,
//                 'amount'     => $payment->jumlah,
//             ],
//             old: $payment->getOriginal(),
//             new: $payment->getAttributes()
//         );
//     }

//     /* ==============================
//      | CORE LOGGER
//      ============================== */
//     protected function log(
//         Payments $payment,
//         string $action,
//         array $meta = [],
//         array|null $old = null,
//         array|null $new = null
//     ): void {
//         [$context, $actor] = $this->resolveContextWithActor();

//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => $action,
//             'context'    => $context, // 🔥 SEKARANG TERISI
//             'meta'       => array_merge($meta, [
//                 'actor' => $actor,
//             ]),
//             'old_value'  => $old,
//             'new_value'  => $new,
//             'created_by' => auth()->id(),
//             'created_at' => now(),
//         ]);
//     }

//     /* ==============================
//      | CONTEXT + ACTOR NAME
//      ============================== */
//     protected function resolveContextWithActor(): array
//     {
//         $user = auth()->user();

//         if (! $user) {
//             return [PaymentLogs::CONTEXT_SYSTEM, 'SYSTEM'];
//         }

//         if ($user->isAgent()) {
//             return [
//                 PaymentLogs::CONTEXT_AGENT,
//                 optional($user->agent)->nama ?? $user->nama
//             ];
//         }

//         if ($user->isCabang()) {
//             return [
//                 PaymentLogs::CONTEXT_CABANG,
//                 optional($user->branch)->nama_cabang ?? 'CABANG'
//             ];
//         }

//         return [
//             PaymentLogs::CONTEXT_KEUANGAN,
//             $user->nama
//         ];
//     }
//     /* ==============================
//      | REJECT PAYMENT
//      ============================== */
//     public function reject(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_REJECT,
//             [
//                 'reason' => $payment->reject_note,
//             ],
//             $payment->getOriginal(),
//             $payment->getAttributes()
//         );
//     }

//     /* ==============================
//      | UPDATE PAYMENT (NON STATUS)
//      ============================== */
//     public function update(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_UPDATE,
//             [],
//             $payment->getOriginal(),
//             $payment->getChanges()
//         );
//     }

//     /* ==============================
//      | DELETE PAYMENT
//      ============================== */
//     public function delete(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_DELETE,
//             [],
//             $payment->getOriginal()
//         );
//     }

// }

// namespace App\Services\Payment;

// use App\Models\PaymentLogs;
// use App\Models\Payments;

// class PaymentLogService
// {
//     /* ==============================
//      | INPUT PAYMENT
//      ============================== */
//     public function input(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_INPUT,
//             [
//                 'amount' => $payment->jumlah,
//                 'method' => $payment->metode,
//             ]
//         );
//     }

//     /* ==============================
//      | APPROVE PAYMENT
//      ============================== */
//     public function approve(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_APPROVE,
//             [
//                 'invoice_id' => $payment->invoice_id,
//                 'amount'     => $payment->jumlah,
//             ],
//             $payment->getOriginal(),
//             $payment->getAttributes()
//         );
//     }

//     /* ==============================
//      | REJECT PAYMENT
//      ============================== */
//     public function reject(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_REJECT,
//             [
//                 'reason' => $payment->reject_note,
//             ],
//             $payment->getOriginal(),
//             $payment->getAttributes()
//         );
//     }

//     /* ==============================
//      | UPDATE PAYMENT (NON STATUS)
//      ============================== */
//     public function update(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_UPDATE,
//             [],
//             $payment->getOriginal(),
//             $payment->getChanges()
//         );
//     }

//     /* ==============================
//      | DELETE PAYMENT
//      ============================== */
//     public function delete(Payments $payment): void
//     {
//         $this->log(
//             $payment,
//             PaymentLogs::ACTION_DELETE,
//             [],
//             $payment->getOriginal()
//         );
//     }

//     /* ==============================
//      | CORE LOGGER (SATU PINTU)
//      ============================== */
//     protected function log(
//         Payments $payment,
//         string $action,
//         array $meta = [],
//         array|null $old = null,
//         array|null $new = null
//     ): void {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => $action,
//             'meta'       => array_merge($meta, [
//                 'context' => $this->resolveContext(),
//             ]),
//             'old_value'  => $old,
//             'new_value'  => $new,
//             'created_by' => auth()->id(),
//             'created_at' => now(),
//         ]);
//     }

//     /* ==============================
//      | RESOLVE CONTEXT
//      ============================== */
//     protected function resolveContext(): string
//     {
//         $user = auth()->user();

//         if (! $user) {
//             return PaymentLogs::CONTEXT_SYSTEM;
//         }

//         if ($user->isAgent()) {
//             return PaymentLogs::CONTEXT_AGENT;
//         }

//         if ($user->isCabang()) {
//             return PaymentLogs::CONTEXT_CABANG;
//         }

//         return PaymentLogs::CONTEXT_KEUANGAN;
//     }
// }

// namespace App\Services\Payment;

// use App\Models\PaymentLogs;
// use App\Models\Payments;
// use App\Models\User;

// class PaymentLogService
// {
//     /* ==============================
//      | INPUT PAYMENT
//      ============================== */
//     public function input(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_INPUT,
//             'context'    => $this->resolveContext(),
//             'meta'       => [
//                 'amount' => $payment->jumlah,
//                 'method'=> $payment->metode,
//             ],
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | INPUT BY AGENT
//      ============================== */
//     public function inputAgent(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_INPUT_AGENT,
//             'context'    => PaymentLogs::CONTEXT_AGENT,
//             'meta'       => [
//                 'amount' => $payment->jumlah,
//                 'method'=> $payment->metode,
//             ],
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | APPROVE
//      ============================== */
//     public function approve(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_APPROVE,
//             'context'    => $this->resolveContext(),
//             'old_value'  => $payment->getOriginal(),
//             'new_value'  => $payment->getAttributes(),
//             'meta'       => [
//                 'invoice_id' => $payment->invoice_id,
//                 'amount'     => $payment->jumlah,
//             ],
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | REJECT
//      ============================== */
//     public function reject(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_REJECT,
//             'context'    => $this->resolveContext(),
//             'old_value'  => $payment->getOriginal(),
//             'new_value'  => $payment->getAttributes(),
//             'meta'       => [
//                 'reason' => $payment->reject_note,
//             ],
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | UPDATE DATA (NON STATUS)
//      ============================== */
//     public function update(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_UPDATE,
//             'context'    => $this->resolveContext(),
//             'old_value'  => $payment->getOriginal(),
//             'new_value'  => $payment->getChanges(),
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | DELETE
//      ============================== */
//     public function delete(Payments $payment): void
//     {
//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => PaymentLogs::ACTION_DELETE,
//             'context'    => $this->resolveContext(),
//             'old_value'  => $payment->getOriginal(),
//             'created_by' => auth()->id(),
//         ]);
//     }

//     /* ==============================
//      | RESOLVE CONTEXT
//      ============================== */
//     protected function resolveContext(): string
//     {
//         $user = auth()->user();

//         if (!$user) {
//             return PaymentLogs::CONTEXT_SYSTEM;
//         }

//         if ($user->isAgent()) {
//             return PaymentLogs::CONTEXT_AGENT;
//         }

//         if ($user->isCabang()) {
//             return 'CABANG';
//         }

//         return PaymentLogs::CONTEXT_KEUANGAN;
//     }
// }

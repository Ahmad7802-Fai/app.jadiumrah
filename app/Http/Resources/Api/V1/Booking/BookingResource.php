<?php

namespace App\Http\Resources\Api\V1\Booking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        /*
        |--------------------------------------------------------------------------
        | 🔥 USE PRELOADED AGGREGATE (NO QUERY)
        |--------------------------------------------------------------------------
        */

        $totalPaid = (float) ($this->paid_total ?? $this->paid_amount ?? 0);
        $total     = (float) $this->total_amount;
        $remaining = max(0, $total - $totalPaid);

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $status    = $this->resolveEffectiveStatus();
        $isExpired = $status === 'expired';

        $paymentStatus = $this->resolvePaymentStatusDynamic(
            $totalPaid,
            $total
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | BASIC
            |--------------------------------------------------------------------------
            */
            'id'             => $this->id,
            'booking_code'   => $this->booking_code,
            'invoice_number' => $this->invoice_number,

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            'status'               => $status,
            'status_label'         => $this->statusLabel($status),
            'original_status'      => $this->status,

            'payment_status'       => $paymentStatus,
            'payment_status_label'=> $this->paymentStatusLabel($paymentStatus),

            'is_expired'           => $isExpired,

            /*
            |--------------------------------------------------------------------------
            | ROOM
            |--------------------------------------------------------------------------
            */
            'room_type'        => $this->room_type,
            'room_type_label'  => $this->roomTypeLabel($this->room_type),
            'qty'              => (int) $this->qty,

            /*
            |--------------------------------------------------------------------------
            | 🔥 PRICE (SYNC FE)
            |--------------------------------------------------------------------------
            */
            'price' => [
                'per_person' => [
                    'value' => (float) $this->price_per_person_snapshot,
                    'label' => $this->formatRupiah($this->price_per_person_snapshot),
                ],

                'total' => [
                    'value' => $total,
                    'label' => $this->formatRupiah($total),
                ],

                'paid' => [
                    'value' => $totalPaid,
                    'label' => $this->formatRupiah($totalPaid),
                ],

                'remaining' => [
                    'value' => $remaining,
                    'label' => $this->formatRupiah($remaining),
                ],

                'original_price' => $this->original_price_snapshot
                    ? (float) $this->original_price_snapshot
                    : null,

                'original_price_label' => $this->original_price_snapshot
                    ? $this->formatRupiah($this->original_price_snapshot)
                    : null,

                'discount' => $this->discount_snapshot
                    ? (float) $this->discount_snapshot
                    : null,

                'discount_label' => $this->discount_snapshot
                    ? $this->formatRupiah($this->discount_snapshot)
                    : null,

                'promo_label' => $this->promo_label_snapshot,
            ],

            /*
            |--------------------------------------------------------------------------
            | 🔥 FLAT (FE WAJIB)
            |--------------------------------------------------------------------------
            */
            'total_amount' => $total,
            'paid_amount'  => $totalPaid,
            'remaining'    => $remaining,

            /*
            |--------------------------------------------------------------------------
            | TIME
            |--------------------------------------------------------------------------
            */
            'expired_at'       => $this->expired_at,
            'expired_at_label' => $this->formatDateTime($this->expired_at),

            'created_at'       => $this->created_at,
            'created_at_label' => $this->formatDateTime($this->created_at),

            /*
            |--------------------------------------------------------------------------
            | ACTIONS
            |--------------------------------------------------------------------------
            */
            'actions' => [
                'can_pay'          => $this->canPay($status, $remaining),
                'can_cancel'       => $this->canCancel($status),
                'can_confirm'      => $this->canConfirm($status, $totalPaid, $total),
                'can_view_invoice' => $this->canViewInvoice(),
            ],

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            'paket' => $this->whenLoaded('paket', fn () => [
                'id'   => $this->paket?->id,
                'name' => $this->paket?->name,
                'slug' => $this->paket?->slug,
            ]),

            'departure' => $this->whenLoaded('departure', fn () => [
                'id' => $this->departure?->id,
                'departure_date' => $this->departure?->departure_date,
                'departure_date_label' => $this->formatDate($this->departure?->departure_date),
                'return_date' => $this->departure?->return_date,
                'return_date_label' => $this->formatDate($this->departure?->return_date),
            ]),

            'jamaahs' => $this->whenLoaded('jamaahs', fn () =>
                $this->jamaahs->map(fn ($j) => [
                    'id'   => $j->id,
                    'name' => $j->nama_lengkap,
                    'price' => [
                        'value' => (float) $j->pivot->price,
                        'label' => $this->formatRupiah($j->pivot->price),
                    ],
                ])
            ),

            'links' => [
                'self'     => route('api.bookings.show', $this->booking_code),
                'invoice'  => route('api.bookings.show', $this->booking_code) . '/invoice',
                'payments' => route('api.bookings.show', $this->booking_code) . '/payments',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS LOGIC
    |--------------------------------------------------------------------------
    */

    protected function resolveEffectiveStatus(): string
    {
        if (
            in_array($this->status, ['waiting_payment', 'partial_paid']) &&
            $this->expired_at &&
            now()->gt($this->expired_at)
        ) {
            return 'expired';
        }

        return $this->status;
    }

    protected function resolvePaymentStatusDynamic($paid, $total): string
    {
        if ($paid >= $total && $total > 0) {
            return 'lunas';
        }

        if ($paid > 0) {
            return 'partial';
        }

        return 'belum_lunas';
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function canPay(string $status, float $remaining): bool
    {
        return in_array($status, ['waiting_payment', 'partial_paid'])
            && !$this->isExpired()
            && $remaining > 0;
    }

    protected function canCancel(string $status): bool
    {
        return in_array($status, ['draft', 'waiting_payment', 'partial_paid']);
    }

    protected function canConfirm(string $status, float $paid, float $total): bool
    {
        return $status === 'partial_paid' || $paid >= $total;
    }

    protected function canViewInvoice(): bool
    {
        return !empty($this->invoice_number);
    }

    protected function isExpired(): bool
    {
        return $this->expired_at && now()->gt($this->expired_at);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMATTERS
    |--------------------------------------------------------------------------
    */

    protected function formatRupiah($amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    protected function formatDateTime($date): ?string
    {
        return $date ? Carbon::parse($date)->translatedFormat('d M Y H:i') : null;
    }

    protected function formatDate($date): ?string
    {
        return $date ? Carbon::parse($date)->translatedFormat('d M Y') : null;
    }

    protected function statusLabel($status): string
    {
        return match ($status) {
            'waiting_payment' => 'Menunggu Pembayaran',
            'partial_paid'    => 'Sebagian Terbayar',
            'confirmed'       => 'Terkonfirmasi',
            'cancelled'       => 'Dibatalkan',
            'expired'         => 'Kedaluwarsa',
            default           => ucfirst($status),
        };
    }

    protected function paymentStatusLabel($status): string
    {
        return match ($status) {
            'belum_lunas' => 'Belum Lunas',
            'partial'     => 'Sebagian',
            'lunas'       => 'Lunas',
            default       => $status,
        };
    }

    protected function roomTypeLabel($type): ?string
    {
        return match ($type) {
            'double' => 'Double',
            'triple' => 'Triple',
            'quad'   => 'Quad',
            default  => $type,
        };
    }
}
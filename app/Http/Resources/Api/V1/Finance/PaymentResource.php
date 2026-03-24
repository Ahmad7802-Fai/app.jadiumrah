<?php

namespace App\Http\Resources\Api\V1\Finance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = (string) $this->status;
        $type = (string) $this->type;
        $method = (string) $this->method;
        $channel = (string) $this->channel;
        $actions = $this->actions($status);

        return [
            'id' => $this->id,
            'payment_code' => $this->payment_code,
            'receipt_number' => $this->receipt_number,
            'invoice_number' => $this->invoice_number,
            'reference_number' => $this->reference_number,

            'type' => $type,
            'type_label' => $this->typeLabel($type),

            'method' => $method,
            'method_label' => $this->methodLabel($method),

            'channel' => $channel,
            'channel_label' => $this->channelLabel($channel),

            'status' => $status,
            'status_label' => $this->statusLabel($status),

            'amount' => (float) $this->amount,
            'amount_label' => $this->formatRupiah($this->amount),

            'fee_amount' => (float) $this->fee_amount,
            'fee_amount_label' => $this->formatRupiah($this->fee_amount),

            'net_amount' => (float) $this->net_amount,
            'net_amount_label' => $this->formatRupiah($this->net_amount),

            'paid_at' => $this->paid_at,
            'paid_at_label' => $this->formatDateTime($this->paid_at),

            'approved_at' => $this->approved_at,
            'approved_at_label' => $this->formatDateTime($this->approved_at),

            'created_at' => $this->created_at,
            'created_at_label' => $this->formatDateTime($this->created_at),

            'note' => $this->note,
            'proof_file' => $this->proof_file,
            'proof_file_url' => $this->proofFileUrl(),

            'actions' => $actions,

            'booking' => $this->whenLoaded('booking', function () {
                $booking = $this->booking;
                $totalAmount = (float) ($booking->total_amount ?? 0);
                $paidAmount = (float) ($booking->paid_amount ?? 0);
                $remaining = max(0, $totalAmount - $paidAmount);

                $originalStatus = (string) ($booking?->status ?? '');
                $effectiveStatus = $this->effectiveBookingStatus($booking);
                $isExpired = $this->bookingIsExpired($booking);

                return [
                    'id' => $booking?->id,
                    'booking_code' => $booking?->booking_code,
                    'invoice_number' => $booking?->invoice_number,

                    'original_status' => $originalStatus,
                    'status' => $effectiveStatus,
                    'status_label' => $this->bookingStatusLabel($effectiveStatus),
                    'is_expired' => $isExpired,

                    'total_amount' => $totalAmount,
                    'total_amount_label' => $this->formatRupiah($totalAmount),

                    'paid_amount' => $paidAmount,
                    'paid_amount_label' => $this->formatRupiah($paidAmount),

                    'remaining' => $remaining,
                    'remaining_label' => $this->formatRupiah($remaining),

                    'expired_at' => $booking?->expired_at,
                    'expired_at_label' => $this->formatDateTime($booking?->expired_at),

                    'paket' => $booking?->paket ? [
                        'id' => $booking->paket->id,
                        'name' => $booking->paket->name,
                        'slug' => $booking->paket->slug,
                    ] : null,

                    'departure' => $booking?->departure ? [
                        'id' => $booking->departure->id,
                        'departure_date' => $booking->departure->departure_date,
                        'departure_date_label' => $this->formatDate($booking->departure->departure_date),
                        'return_date' => $booking->departure->return_date,
                        'return_date_label' => $this->formatDate($booking->departure->return_date),
                    ] : null,
                ];
            }),

            'jamaah' => $this->whenLoaded('jamaah', function () {
                if (!$this->jamaah) {
                    return null;
                }

                return [
                    'id' => $this->jamaah->id,
                    'nama_lengkap' => $this->jamaah->nama_lengkap,
                ];
            }),

            'links' => [
                'self' => route('api.payments.show', $this->id),
                'approve' => $actions['can_approve']
                    ? route('api.payments.approve', $this->id)
                    : null,
                'reject' => $actions['can_reject']
                    ? route('api.payments.reject', $this->id)
                    : null,
                'cancel' => $actions['can_cancel']
                    ? route('api.payments.cancel', $this->id)
                    : null,
                'booking' => $this->relationLoaded('booking') && $this->booking
                    ? route('api.bookings.show', $this->booking->booking_code)
                    : null,
            ],
        ];
    }

    protected function actions(string $status): array
    {
        $bookingExpired = $this->relationLoaded('booking') && $this->booking
            ? $this->bookingIsExpired($this->booking)
            : false;

        $isPending = $status === 'pending';

        return [
            'can_edit' => $isPending && !$bookingExpired,
            'can_approve' => $isPending && !$bookingExpired,
            'can_reject' => $isPending,
            'can_cancel' => $isPending,
            'can_delete' => in_array($status, ['pending', 'failed', 'cancelled'], true),
        ];
    }

    protected function bookingIsExpired($booking): bool
    {
        if (!$booking || !$booking->expired_at) {
            return false;
        }

        return Carbon::parse($booking->expired_at)->lte(now())
            && in_array((string) $booking->status, ['waiting_payment', 'partial_paid'], true);
    }

    protected function effectiveBookingStatus($booking): string
    {
        if (!$booking) {
            return '';
        }

        if ($this->bookingIsExpired($booking)) {
            return 'expired';
        }

        return (string) $booking->status;
    }

    protected function bookingStatusLabel(?string $status): ?string
    {
        return match ($status) {
            'waiting_payment' => 'Menunggu Pembayaran',
            'partial_paid' => 'Sebagian Terbayar',
            'confirmed' => 'Terkonfirmasi',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            default => $status,
        };
    }

    protected function statusLabel(?string $status): ?string
    {
        return match ($status) {
            'pending' => 'Menunggu Approval',
            'paid' => 'Dibayar',
            'failed' => 'Ditolak / Gagal',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    protected function typeLabel(?string $type): ?string
    {
        return match ($type) {
            'dp' => 'DP',
            'cicilan' => 'Cicilan',
            'pelunasan' => 'Pelunasan',
            'add_on' => 'Add On',
            'upgrade' => 'Upgrade',
            'adjustment' => 'Adjustment',
            default => $type,
        };
    }

    protected function methodLabel(?string $method): ?string
    {
        return match ($method) {
            'transfer' => 'Transfer',
            'cash' => 'Cash',
            'gateway' => 'Payment Gateway',
            'edc' => 'EDC',
            default => $method,
        };
    }

    protected function channelLabel(?string $channel): ?string
    {
        return match ($channel) {
            'website' => 'Website',
            'agent' => 'Agent',
            'admin' => 'Admin',
            'gateway' => 'Gateway',
            default => $channel,
        };
    }

    protected function formatRupiah($amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    protected function formatDateTime($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->translatedFormat('d M Y H:i');
    }

    protected function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->translatedFormat('d M Y');
    }

    protected function proofFileUrl(): ?string
    {
        if (!$this->proof_file) {
            return null;
        }

        return asset('storage/' . ltrim($this->proof_file, '/'));
    }
}
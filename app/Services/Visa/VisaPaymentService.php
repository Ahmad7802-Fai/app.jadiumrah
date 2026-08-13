<?php

namespace App\Services\Visa;

use App\Models\VisaOrder;
use App\Models\VisaPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VisaPaymentService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return VisaPayment::query()
            ->with(['order'])
            ->when(!empty($filters['payment_status']), function ($query) use ($filters) {
                $query->where('payment_status', $filters['payment_status']);
            })
            ->when(!empty($filters['payment_method']), function ($query) use ($filters) {
                $query->where('payment_method', $filters['payment_method']);
            })
            ->when(!empty($filters['keyword']), function ($query) use ($filters) {
                $keyword = trim($filters['keyword']);
                $query->where(function ($q) use ($keyword) {
                    $q->where('payment_number', 'like', "%{$keyword}%")
                        ->orWhere('reference_number', 'like', "%{$keyword}%")
                        ->orWhereHas('order', function ($orderQuery) use ($keyword) {
                            $orderQuery->where('order_number', 'like', "%{$keyword}%")
                                ->orWhere('customer_name', 'like', "%{$keyword}%")
                                ->orWhere('customer_phone', 'like', "%{$keyword}%");
                        });
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(VisaOrder $order, array $data): VisaPayment
    {
        return DB::transaction(function () use ($order, $data) {
            $payment = $order->payments()->create([
                'payment_method' => $data['payment_method'] ?? VisaPayment::METHOD_BANK_TRANSFER,
                'amount' => $data['amount'],
                'payment_status' => $data['payment_status'] ?? VisaPayment::STATUS_PENDING,
                'reference_number' => $data['reference_number'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'account_name' => $data['account_name'] ?? null,
                'paid_at' => $data['paid_at'] ?? null,
                'note' => $data['note'] ?? null,
                'confirmed_by' => $data['confirmed_by'] ?? null,
                'confirmed_at' => $data['confirmed_at'] ?? null,
            ]);

            $order->refresh()->recalculatePayment();

            return $payment->fresh(['order']);
        });
    }

    public function markAsPaid(
        VisaPayment $payment,
        ?string $referenceNumber = null,
        ?int $confirmedBy = null,
        ?string $note = null
    ): VisaPayment {
        return DB::transaction(function () use ($payment, $referenceNumber, $confirmedBy, $note) {
            $payment->update([
                'payment_status' => VisaPayment::STATUS_PAID,
                'reference_number' => $referenceNumber ?? $payment->reference_number,
                'paid_at' => $payment->paid_at ?? now(),
                'confirmed_by' => $confirmedBy,
                'confirmed_at' => now(),
                'note' => $note ?? $payment->note,
            ]);

            $payment->order?->recalculatePayment();

            return $payment->fresh(['order']);
        });
    }

    public function markAsFailed(VisaPayment $payment, ?string $note = null): VisaPayment
    {
        return DB::transaction(function () use ($payment, $note) {
            $payment->update([
                'payment_status' => VisaPayment::STATUS_FAILED,
                'note' => $note ?? $payment->note,
            ]);

            $payment->order?->recalculatePayment();

            return $payment->fresh(['order']);
        });
    }

    public function markAsRefunded(VisaPayment $payment, ?string $note = null): VisaPayment
    {
        return DB::transaction(function () use ($payment, $note) {
            $payment->update([
                'payment_status' => VisaPayment::STATUS_REFUNDED,
                'note' => $note ?? $payment->note,
            ]);

            $payment->order?->recalculatePayment();

            return $payment->fresh(['order']);
        });
    }

    public function delete(VisaPayment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $order = $payment->order;
            $deleted = (bool) $payment->delete();

            if ($order) {
                $order->refresh()->recalculatePayment();
            }

            return $deleted;
        });
    }
}
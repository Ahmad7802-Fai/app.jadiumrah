<?php

namespace App\Services\Visa;

use App\Models\VisaOrder;
use Illuminate\Support\Facades\DB;

class VisaStatusService
{
    public function updateStatus(
        VisaOrder $order,
        string $toStatus,
        ?string $description = null,
        ?int $changedBy = null
    ): VisaOrder {
        return DB::transaction(function () use ($order, $toStatus, $description, $changedBy) {
            $fromStatus = $order->order_status;

            $payload = [
                'order_status' => $toStatus,
            ];

            if ($toStatus === VisaOrder::STATUS_SUBMITTED && !$order->submitted_at) {
                $payload['submitted_at'] = now();
            }

            if ($toStatus === VisaOrder::STATUS_APPROVED && !$order->approved_at) {
                $payload['approved_at'] = now();
            }

            if ($toStatus === VisaOrder::STATUS_COMPLETED && !$order->completed_at) {
                $payload['completed_at'] = now();
            }

            if ($toStatus === VisaOrder::STATUS_CANCELLED && !$order->cancelled_at) {
                $payload['cancelled_at'] = now();
            }

            $order->update($payload);

            $order->statusHistories()->create([
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'description' => $description,
                'changed_by' => $changedBy,
                'changed_at' => now(),
            ]);

            return $order->fresh([
                'product',
                'user',
                'travelers',
                'documents',
                'payments',
                'statusHistories',
                'notes',
            ]);
        });
    }
}
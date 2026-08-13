<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use Exception;

class JamaahApprovalService
{
    /**
     * Approve jamaah
     */
    public function approve(Jamaah $jamaah): void
    {
        
        if ($jamaah->status !== 'pending') {
            throw new Exception('Jamaah sudah diproses.');
        }

        DB::transaction(function () use ($jamaah) {

            $jamaah->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            JamaahAuditService::log(
                $jamaah,
                'APPROVED',
                ['status' => 'pending'],
                ['status' => 'approved']
            );
        });
    }

    /**
     * Reject jamaah
     */
    public function reject(Jamaah $jamaah, string $reason): void
    {
        if ($jamaah->status !== 'pending') {
            throw new Exception('Jamaah sudah diproses.');
        }

        DB::transaction(function () use ($jamaah, $reason) {

            $jamaah->update([
                'status'          => 'rejected',
                'rejected_reason' => $reason,
                'approved_at'     => null,
                'approved_by'     => null,
            ]);

            JamaahAuditService::log(
                $jamaah,
                'REJECTED',
                ['status' => 'pending'],
                [
                    'status' => 'rejected',
                    'reason' => $reason,
                ]
            );
        });
    }
}

<?php

namespace App\Services\Komisi;

use App\Models\KomisiLogs;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KomisiApprovalService
{
    /**
     * pending → available
     */
    public function approve(int $komisiId): KomisiLogs
    {
        return DB::transaction(function () use ($komisiId) {

            $komisi = KomisiLogs::lockForUpdate()->findOrFail($komisiId);

            if ($komisi->status !== KomisiLogs::STATUS_PENDING) {
                throw new RuntimeException('Komisi sudah diproses.');
            }

            $komisi->update([
                'status' => KomisiLogs::STATUS_AVAILABLE,
            ]);

            return $komisi;
        });
    }

    /**
     * pending → rejected
     */
    public function reject(
        int $komisiId,
        int $adminId,
        string $reason
    ): KomisiLogs {
        return DB::transaction(function () use ($komisiId, $adminId, $reason) {

            $komisi = KomisiLogs::lockForUpdate()->findOrFail($komisiId);

            if ($komisi->status !== KomisiLogs::STATUS_PENDING) {
                throw new RuntimeException('Komisi sudah diproses.');
            }

            $komisi->update([
                'status'        => KomisiLogs::STATUS_REJECTED,
                'rejected_at'   => now(),
                'rejected_by'   => $adminId,
                'reject_reason' => $reason,
            ]);

            return $komisi;
        });
    }
}

// namespace App\Services\Komisi;

// use App\Models\KomisiLogs;
// use Illuminate\Support\Facades\DB;
// use RuntimeException;

// class KomisiApprovalService
// {
//     public function approve(int $komisiId, int $adminId): KomisiLogs
//     {
//         return DB::transaction(function () use ($komisiId, $adminId) {

//             $komisi = KomisiLogs::lockForUpdate()->findOrFail($komisiId);

//             if ($komisi->status !== KomisiLogs::STATUS_PENDING) {
//                 throw new RuntimeException('Komisi sudah diproses.');
//             }

//             $komisi->update([
//                 'status'       => KomisiLogs::STATUS_AVAILABLE,
//                 'approved_at'  => now(),
//                 'approved_by'  => $adminId,
//             ]);

//             return $komisi;
//         });
//     }

//     public function reject(
//         int $komisiId,
//         int $adminId,
//         string $reason
//     ): KomisiLogs {
//         return DB::transaction(function () use ($komisiId, $adminId, $reason) {

//             $komisi = KomisiLogs::lockForUpdate()->findOrFail($komisiId);

//             if ($komisi->status !== KomisiLogs::STATUS_PENDING) {
//                 throw new RuntimeException('Komisi sudah diproses.');
//             }

//             $komisi->update([
//                 'status'        => KomisiLogs::STATUS_REJECTED,
//                 'rejected_at'   => now(),
//                 'rejected_by'   => $adminId,
//                 'reject_reason' => $reason,
//             ]);

//             return $komisi;
//         });
//     }
// }

// namespace App\Services\Komisi;

// use App\Models\KomisiLogs;
// use Illuminate\Support\Facades\DB;
// use RuntimeException;

// class KomisiApprovalService
// {
//     public function makeAvailable(int $komisiId, int $adminId): KomisiLogs
//     {
//         return DB::transaction(function () use ($komisiId, $adminId) {

//             $komisi = KomisiLogs::lockForUpdate()->findOrFail($komisiId);

//             if ($komisi->status !== KomisiLogs::STATUS_PENDING) {
//                 throw new RuntimeException('Komisi sudah diproses.');
//             }

//             $komisi->update([
//                 'status'        => KomisiLogs::STATUS_AVAILABLE,
//                 'approved_at'   => now(),
//                 'approved_by'   => $adminId,
//             ]);

//             return $komisi;
//         });
//     }
// }

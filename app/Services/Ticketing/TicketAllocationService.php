<?php

namespace App\Services\Ticketing;

use App\Models\TicketAllocation;
use App\Models\TicketPnr;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketAllocationService
{
    /* ======================================================
     | CREATE ALLOCATION
     | - Append only
     | - Tidak update PNR
     | - Observer yang tentukan status
     ====================================================== */
    public function allocate(int $pnrId, int $amount): TicketAllocation
    {
        return DB::transaction(function () use ($pnrId, $amount) {

            $pnr = TicketPnr::lockForUpdate()->findOrFail($pnrId);

            if (!in_array($pnr->status, ['ON_FLOW', 'CONFIRMED'])) {
                throw new Exception('PNR tidak bisa dialokasikan.');
            }

            if ($amount <= 0) {
                throw new Exception('Jumlah allocation tidak valid.');
            }

            return TicketAllocation::create([
                'pnr_id'           => $pnr->id,
                'allocated_amount' => $amount,
                'allocation_date'  => now(),
                'status'           => 'ALLOCATED',
            ]);
        });
    }
}

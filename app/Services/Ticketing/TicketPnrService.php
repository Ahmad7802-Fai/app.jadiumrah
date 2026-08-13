<?php

namespace App\Services\Ticketing;

use App\Models\TicketPnr;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketPnrService
{
    /* ======================================================
     | CREATE PNR
     | STATUS AWAL: ON_FLOW
     | ❌ TANPA LOGIKA PAYMENT / DEPOSIT
     ====================================================== */
    public function create(array $data): TicketPnr
    {
        return DB::transaction(function () use ($data) {

            /* ===============================
             | VALID & CAST
             =============================== */
            $pax        = (int) $data['pax'];
            $farePerPax = (int) $data['fare_per_pax'];

            if ($pax <= 0) {
                throw new Exception('Pax harus lebih dari 0.');
            }

            if ($farePerPax < 0) {
                throw new Exception('Fare tidak valid.');
            }

            /* ===============================
             | DERIVED (STATIC REFERENCE ONLY)
             =============================== */
            $totalFare = $pax * $farePerPax;

            /* ===============================
             | CREATE PNR (NO MONEY LOGIC)
             =============================== */
            $pnr = TicketPnr::create([
                'pnr_code'      => $data['pnr_code'],
                'client_id'     => $data['client_id'],
                'agent_id'      => $data['agent_id'] ?? null,
                'branch_id'     => $data['branch_id'] ?? null,
                'category'      => $data['category'] ?? null,

                // ✈️ AIRLINE (VALIDATING CARRIER)
                'airline_code'  => $data['airline_code'] ?? null,
                'airline_name'  => $data['airline_name'] ?? null,
                'airline_class' => $data['airline_class'] ?? null,

                // CORE
                'pax'           => $pax,
                'fare_per_pax'  => $farePerPax,
                'total_fare'    => $totalFare,

                'seat'          => $data['seat'] ?? 0,
                'status'        => 'ON_FLOW',
                'created_by'    => $data['created_by'],
            ]);

            /* ===============================
             | FLIGHT ROUTES
             =============================== */
            foreach ($data['routes'] as $i => $route) {

                $pnr->routes()->create([
                    'sector'              => $i + 1,
                    'origin'              => $route['origin'],
                    // 'destination'         => $route['destination'],
                    'departure_date'      => $route['departure_date'],
                    'departure_time'      => $route['departure_time'] ?? null,
                    'arrival_time'        => $route['arrival_time'] ?? null,
                    'arrival_day_offset'  => $route['arrival_day_offset'] ?? 0,
                    'flight_number'       => $route['flight_number'] ?? null,
                ]);
            }

            /* ===============================
             | AUDIT LOG
             =============================== */
            TicketAuditService::log(
                'PNR',
                $pnr->id,
                'PNR_CREATED',
                null,
                $pnr->fresh()->toArray()
            );

            return $pnr;
        });
    }

    public function update(TicketPnr $pnr, array $data): void
    {
        DB::transaction(function () use ($pnr, $data) {

            if ($pnr->status !== 'ON_FLOW') {
                throw new Exception('PNR tidak bisa diedit');
            }

            if ($pnr->invoices()->exists()) {
                throw new Exception('PNR sudah memiliki invoice');
            }

            $before = $pnr->getOriginal();

            $pnr->update([
                'airline_code'  => $data['airline_code'] ?? null,
                'airline_name'  => $data['airline_name'] ?? null,
                'airline_class' => $data['airline_class'] ?? null,
                'pax'           => $data['pax'],
                'fare_per_pax'  => $data['fare_per_pax'],
                'total_fare'    => $data['pax'] * $data['fare_per_pax'],
            ]);

            // reset routes
            $pnr->routes()->delete();

            foreach ($data['routes'] as $i => $route) {
                $pnr->routes()->create([
                    'sector' => $i + 1,
                    ...$route,
                ]);
            }

            TicketAuditService::log(
                'PNR',
                $pnr->id,
                'PNR_UPDATED',
                $before,
                $pnr->fresh()->toArray()
            );
        });
    }

    /* ======================================================
     | CONFIRM PNR
     | ON_FLOW → CONFIRMED
     ====================================================== */
    public function confirm(TicketPnr $pnr): void
    {
        DB::transaction(function () use ($pnr) {

            // 🔒 LOCK ROW
            $pnr = TicketPnr::where('id', $pnr->id)
                ->lockForUpdate()
                ->first();

            if ($pnr->status !== 'ON_FLOW') {
                throw new Exception('PNR tidak bisa dikonfirmasi.');
            }

            if ($pnr->routes()->count() === 0) {
                throw new Exception('Flight route belum diisi.');
            }

            $before = $pnr->getOriginal();

            $pnr->update([
                'status' => 'CONFIRMED',
            ]);

            TicketAuditService::log(
                'PNR',
                $pnr->id,
                'PNR_CONFIRMED',
                $before,
                $pnr->fresh()->toArray()
            );
        });
    }

    /* ======================================================
     | ISSUE PNR
     | CONFIRMED → ISSUED
     | (INVOICE SUDAH HARUS ADA)
     ====================================================== */
    public function issue(TicketPnr $pnr): void
    {
        if ($pnr->status !== 'CONFIRMED') {
            throw new Exception('PNR belum CONFIRMED.');
        }

        if ($pnr->invoices()->count() === 0) {
            throw new Exception('Invoice belum dibuat.');
        }

        $before = $pnr->getOriginal();

        $pnr->update([
            'status' => 'ISSUED',
        ]);

        TicketAuditService::log(
            'PNR',
            $pnr->id,
            'PNR_ISSUED',
            $before,
            $pnr->fresh()->toArray()
        );
    }

    /* ======================================================
     | REVERT ISSUE
     | ISSUED → CONFIRMED
     ====================================================== */
    public function revertToConfirmed(TicketPnr $pnr): void
    {
        if ($pnr->status !== 'ISSUED') {
            return;
        }

        $before = $pnr->getOriginal();

        $pnr->update([
            'status' => 'CONFIRMED',
        ]);

        TicketAuditService::log(
            'PNR',
            $pnr->id,
            'PNR_REVERTED',
            $before,
            $pnr->fresh()->toArray()
        );
    }

    /* ======================================================
     | CANCEL PNR
     | ❌ ISSUED TIDAK BOLEH
     ====================================================== */
    public function cancel(TicketPnr $pnr): void
    {
        if ($pnr->status === 'ISSUED') {
            throw new Exception('PNR ISSUED tidak bisa dibatalkan.');
        }

        $before = $pnr->getOriginal();

        $pnr->update([
            'status' => 'CANCELLED',
        ]);

        TicketAuditService::log(
            'PNR',
            $pnr->id,
            'PNR_CANCELLED',
            $before,
            $pnr->fresh()->toArray()
        );
    }
}

// namespace App\Services\Ticketing;

// use App\Models\TicketPnr;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class TicketPnrService
// {
//     /* ======================================================
//      | CREATE PNR
//      | STATUS AWAL: ON_FLOW
//      ====================================================== */
//     public function create(array $data): TicketPnr
//     {
//         return DB::transaction(function () use ($data) {

//             /* ===============================
//              | VALID & CAST
//              =============================== */
//             $pax           = (int) $data['pax'];
//             $farePerPax    = (int) $data['fare_per_pax'];
//             $depositPerPax = (int) $data['deposit_per_pax'];

//             /* ===============================
//              | SERVER-SIDE CALC (WAJIB)
//              =============================== */
//             $totalFare    = $pax * $farePerPax;
//             $totalDeposit = $pax * $depositPerPax;
//             $balance      = max(0, $totalFare - $totalDeposit);

//             /* ===============================
//              | CREATE PNR
//              =============================== */
//             $pnr = TicketPnr::create([
//                 'pnr_code'        => $data['pnr_code'],
//                 'client_id'       => $data['client_id'],
//                 'agent_id'        => $data['agent_id'] ?? null,
//                 'branch_id'       => $data['branch_id'] ?? null,
//                 'category'        => $data['category'] ?? null,

//                 // ✈️ AIRLINE (PRIMARY / VALIDATING CARRIER)
//                 'airline_code'    => $data['airline_code'] ?? null,
//                 'airline_name'    => $data['airline_name'] ?? null,
//                 'airline_class'   => $data['airline_class'] ?? null,

//                 // 💺 PRICING
//                 'pax'             => $pax,
//                 'fare_per_pax'    => $farePerPax,
//                 'deposit_per_pax' => $depositPerPax,

//                 'total_fare'      => $totalFare,
//                 'total_deposit'   => $totalDeposit,
//                 'balance'         => $balance,

//                 'seat'            => $data['seat'] ?? 0,
//                 'status'          => 'ON_FLOW',
//                 'created_by'      => $data['created_by'],
//             ]);

//             /* ===============================
//              | FLIGHT ROUTES
//              =============================== */
//             foreach ($data['routes'] as $i => $route) {

//                 $pnr->routes()->create([
//                     'sector'         => $i + 1,
//                     'origin'         => $route['origin'],
//                     'destination'    => $route['destination'],
//                     'departure_date' => $route['departure_date'],

//                     // ✈️ JAM
//                     'departure_time' => $route['departure_time'] ?? null,
//                     'arrival_time'   => $route['arrival_time'] ?? null,
//                     'arrival_day_offset' => $route['arrival_day_offset'] ?? 0,

//                     'flight_number'  => $route['flight_number'] ?? null,
//                 ]);
//             }


//             /* ===============================
//              | AUDIT — CREATE
//              =============================== */
//             TicketAuditService::log(
//                 'PNR',
//                 $pnr->id,
//                 'PNR_CREATED',
//                 null,
//                 $pnr->fresh()->toArray()
//             );

//             return $pnr;
//         });
//     }

//     /* ======================================================
//      | CONFIRM PNR
//      | ON_FLOW → CONFIRMED
//      ====================================================== */
//     public function confirm(TicketPnr $pnr): void
//     {
//         DB::transaction(function () use ($pnr) {

//             /**
//              * 🔒 HARD LOCK ROW (ANTI DOUBLE CONFIRM)
//              */
//             $pnr->lockForUpdate();

//             /**
//              * 🧱 VALIDATION (SETELAH LOCK)
//              */
//             if ($pnr->status !== 'ON_FLOW') {
//                 throw new Exception('PNR tidak bisa dikonfirmasi.');
//             }

//             if ($pnr->routes()->count() === 0) {
//                 throw new Exception('Flight sector belum diisi.');
//             }

//             /**
//              * 📸 SNAPSHOT BEFORE
//              */
//             $before = $pnr->getOriginal();

//             /**
//              * ✅ UPDATE STATUS
//              */
//             $pnr->update([
//                 'status' => 'CONFIRMED',
//                 // optional (kalau kolom ada)
//                 // 'confirmed_at' => now(),
//                 // 'confirmed_by' => auth()->id(),
//             ]);

//             /**
//              * 📝 AUDIT LOG
//              */
//             TicketAuditService::log(
//                 'PNR',
//                 $pnr->id,
//                 'PNR_CONFIRMED',
//                 $before,
//                 $pnr->fresh()->toArray()
//             );
//         });
//     }


//     /* ======================================================
//      | ISSUE PNR
//      | CONFIRMED → ISSUED
//      | (Biasanya dipanggil via Observer)
//      ====================================================== */
//     public function issue(TicketPnr $pnr): void
//     {
//         if ($pnr->status !== 'CONFIRMED') {
//             throw new Exception('PNR belum CONFIRMED.');
//         }

//         $before = $pnr->getOriginal();

//         $pnr->update([
//             'status' => 'ISSUED',
//         ]);

//         TicketAuditService::log(
//             'PNR',
//             $pnr->id,
//             'PNR_ISSUED',
//             $before,
//             $pnr->fresh()->toArray()
//         );
//     }

//     /* ======================================================
//      | REVERT ISSUE
//      | ISSUED → CONFIRMED
//      ====================================================== */
//     public function revertToConfirmed(TicketPnr $pnr): void
//     {
//         if ($pnr->status !== 'ISSUED') {
//             return;
//         }

//         $before = $pnr->getOriginal();

//         $pnr->update([
//             'status' => 'CONFIRMED',
//         ]);

//         TicketAuditService::log(
//             'PNR',
//             $pnr->id,
//             'PNR_REVERTED',
//             $before,
//             $pnr->fresh()->toArray()
//         );
//     }

//     /* ======================================================
//      | CANCEL PNR
//      ====================================================== */
//     public function cancel(TicketPnr $pnr): void
//     {
//         if ($pnr->status === 'ISSUED') {
//             throw new Exception('PNR ISSUED tidak bisa dibatalkan.');
//         }

//         $before = $pnr->getOriginal();

//         $pnr->update([
//             'status' => 'CANCELLED',
//         ]);

//         TicketAuditService::log(
//             'PNR',
//             $pnr->id,
//             'PNR_CANCELLED',
//             $before,
//             $pnr->fresh()->toArray()
//         );
//     }
// }

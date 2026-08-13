<?php

namespace App\Observers;

use App\Models\LeadClosing;
use App\Services\JamaahService;
use Illuminate\Support\Facades\DB;

class LeadClosingObserver
{
    public function updated(LeadClosing $closing)
    {
        if ($closing->wasChanged('status') && $closing->status === 'APPROVED') {

            $lead = $closing->lead;

            $lead->update([
                'status'    => 'CLOSED',
                'closed_at'=> now(),
            ]);
        }
    }

}


// namespace App\Observers;

// use App\Models\LeadClosing;
// use App\Services\JamaahService;
// use Illuminate\Support\Facades\DB;

// class LeadClosingObserver
// {
//     public function updated(LeadClosing $closing): void
//     {
//         // 🛑 GUARD: hanya jalan SEKALI saat baru APPROVED
//         if (
//             $closing->getOriginal('status') === 'APPROVED' ||
//             $closing->status !== 'APPROVED'
//         ) {
//             return;
//         }

//         DB::transaction(function () use ($closing) {

//             $lead = $closing->lead;

//             // 1️⃣ UPDATE LEAD (AMAN)
//             $lead->updateQuietly([
//                 'status'     => 'CLOSED',
//                 'closed_at'  => now(),
//             ]);

//             // 2️⃣ CREATE JAMAAH VIA SERVICE
//             $jamaah = app(JamaahService::class)
//                 ->createFromClosing([
//                     'nama_lengkap' => $lead->nama,
//                     'no_hp'        => $lead->no_hp,
//                     'branch_id'    => $lead->branch_id,
//                     'agent_id'     => $lead->agent_id,
//                 ]);

//             // 3️⃣ UPDATE CLOSING TANPA TRIGGER OBSERVER
//             $closing->updateQuietly([
//                 'jamaah_id' => $jamaah->id,
//                 'closed_at'=> now(),
//             ]);
//         });
//     }
// }

// namespace App\Observers;

// use App\Models\LeadClosing;
// use Illuminate\Support\Facades\DB;
// use App\Services\JamaahService;

// class LeadClosingObserver
// {
//     public function updated(LeadClosing $closing): void
//     {
//         // hanya jalan saat APPROVED
//         if ($closing->status !== 'APPROVED') {
//             return;
//         }

//         $lead = $closing->lead;

//         DB::transaction(function () use ($closing, $lead) {

//             // 1️⃣ UPDATE LEAD → CLOSED
//             $lead->update([
//                 'status'    => 'CLOSED',
//                 'closed_at'=> now(),
//             ]);

//             // 2️⃣ CREATE JAMAAH VIA SERVICE (WAJIB)
//             $jamaah = app(JamaahService::class)
//                 ->createFromClosing([
//                     'nama_lengkap' => $lead->nama,
//                     'no_hp'        => $lead->no_hp,
//                     'branch_id'    => $lead->branch_id,
//                     'agent_id'     => $lead->agent_id,
//                 ]);

//             // 3️⃣ LINK CLOSING → JAMAAH
//             $closing->update([
//                 'jamaah_id' => $jamaah->id,
//                 'closed_at'=> now(),
//             ]);
//         });
//     }
// }

// namespace App\Observers;

// use App\Models\LeadClosing;
// use App\Models\Jamaah;
// use Illuminate\Support\Facades\DB;

// class LeadClosingObserver
// {
//     public function updated(LeadClosing $closing): void
//     {
//         // hanya saat APPROVED
//         if ($closing->status !== 'APPROVED') {
//             return;
//         }

//         $lead = $closing->lead;

//         if (!$lead) {
//             return;
//         }

//         DB::transaction(function () use ($closing, $lead) {

//             // 1️⃣ UPDATE LEAD → CLOSED
//             $lead->update([
//                 'status'    => 'CLOSED',
//                 'closed_at' => now(),
//             ]);

//             // 2️⃣ CEK: JANGAN DUPLIKAT JAMAAH
//             if ($lead->jamaah) {
//                 return;
//             }

//             // 3️⃣ CREATE JAMAAH
//             $jamaah = Jamaah::create([
//                 'nama_lengkap' => $lead->nama,
//                 'no_hp'        => $lead->no_hp,
//                 'email'        => $lead->email,
//                 'agent_id'     => $closing->agent_id ?? $lead->agent_id,
//                 'branch_id'    => $closing->branch_id ?? $lead->branch_id,
//                 'status'       => 'approved',
//                 'lead_id'      => $lead->id,
//             ]);

//             // 4️⃣ LINK JAMAAH KE CLOSING
//             $closing->updateQuietly([
//                 'jamaah_id' => $jamaah->id,
//                 'closed_at' => now(),
//             ]);
//         });
//     }
// }

<?php

namespace App\Observers;

use App\Models\LeadActivity;
use App\Models\Lead;

class LeadActivityObserver
{
    /**
     * Setelah activity dibuat
     */
    public function created(LeadActivity $activity): void
    {
        $lead = Lead::find($activity->lead_id);
        if (!$lead) return;

        match ($activity->aktivitas) {

            'lost' => $lead->update([
                'status'      => 'DROPPED',
                'dropped_at'  => now(),
                'drop_reason' => $activity->hasil
            ]),

            default => $lead->update([
                'status' => 'ACTIVE'
            ]),
        };
    }

}

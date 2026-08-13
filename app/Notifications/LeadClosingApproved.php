<?php

namespace App\Notifications;

use App\Models\LeadClosing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeadClosingApproved extends Notification
{
    use Queueable;

    public function __construct(
        protected LeadClosing $closing
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'      => 'CLOSING_APPROVED',
            'lead_id'   => $this->closing->lead_id,
            'lead_name' => $this->closing->lead->nama,
            'message'   => 'Closing lead telah DISETUJUI pusat.',
        ];
    }
}

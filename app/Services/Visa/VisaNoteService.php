<?php

namespace App\Services\Visa;

use App\Models\VisaOrder;
use App\Models\VisaOrderNote;

class VisaNoteService
{
    public function create(
        VisaOrder $order,
        string $note,
        string $noteType = VisaOrderNote::TYPE_INTERNAL,
        ?int $userId = null
    ): VisaOrderNote {
        return $order->notes()->create([
            'user_id' => $userId,
            'note_type' => $noteType,
            'note' => $note,
        ]);
    }
}
<?php

namespace App\Services;

use App\Models\JamaahNotification;

class JamaahNotificationService
{
    public static function topupApproved($jamaahId, $amount)
    {
        JamaahNotification::create([
            'jamaah_id' => $jamaahId,
            'title'     => 'Top Up Berhasil',
            'message'   => 'Top up sebesar Rp ' .
                number_format($amount,0,',','.') .
                ' telah dikonfirmasi.',
            'is_read'   => 0,
        ]);
    }
}

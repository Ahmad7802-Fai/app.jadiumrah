<?php

namespace App\Services;

use App\Models\BuktiSetoran;

class BuktiNumber
{
    public static function generate(): string
    {
        $date = now()->format('Ymd');

        $count = BuktiSetoran::whereDate('created_at', now())->count() + 1;

        return 'BSTU-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

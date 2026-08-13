<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Models\JamaahAudit;

class JamaahAuditService
{
    public static function log(
        Jamaah $jamaah,
        string $action,
        ?array $old,
        ?array $new
    ): JamaahAudit {
        return JamaahAudit::create([
            'jamaah_id'     => $jamaah->id,
            'action'        => $action,
            'old_data'      => $old,
            'new_data'      => $new,
            'performed_by'  => auth()->id(),
        ]);
    }
}
